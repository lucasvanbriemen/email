<?php

namespace App\Console\Commands;

use App\Jobs\FetchEmailsForProfileJob;
use App\Models\ImapCredentials;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(
    name: 'emails:dispatch',
    description: 'Continuously dispatch email fetching jobs for all profiles'
)]
class DispatchEmailFetchingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:dispatch {--once : Run once instead of continuous loop}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Continuously dispatch email fetching jobs for all profiles at configured intervals';

    /**
     * Should stop flag for graceful shutdown
     */
    private bool $shouldStop = false;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!config('email_fetching.enabled')) {
            $this->error('Email fetching is disabled. Enable it in config/email_fetching.php or set EMAIL_FETCH_ENABLED=true');
            return 1;
        }

        // Set up signal handlers for graceful shutdown
        if (extension_loaded('pcntl')) {
            pcntl_signal(SIGTERM, fn() => $this->shouldStop = true);
            pcntl_signal(SIGINT, fn() => $this->shouldStop = true);
        }

        $fetchInterval = config('email_fetching.fetch_interval', 30);
        $runOnce = $this->option('once');

        $this->info('Email fetching dispatcher started');
        $this->info("Fetch interval: {$fetchInterval} seconds");
        if (!$runOnce) {
            $this->info('Press CTRL+C to stop');
        }

        do {
            try {
                $this->dispatchJobs();
            } catch (\Exception $e) {
                $this->error("Error during dispatch: " . $e->getMessage());
                Log::error("Email dispatcher error: " . $e->getMessage(), ['exception' => $e]);
            }

            if ($runOnce) {
                break;
            }

            // Sleep for configured interval
            if (!$this->shouldStop) {
                sleep($fetchInterval);
            }

            // Dispatch PCNTL signals if extension is loaded
            if (extension_loaded('pcntl')) {
                pcntl_signal_dispatch();
            }

        } while (!$this->shouldStop);

        $this->info('Email fetching dispatcher stopped gracefully');
        Log::info('Email fetching dispatcher stopped');

        return 0;
    }

    /**
     * Dispatch jobs for all IMAP credentials
     */
    private function dispatchJobs(): void
    {
        // Get all IMAP credentials with their profiles
        $credentials = ImapCredentials::with('profile')->get();

        if ($credentials->isEmpty()) {
            $this->warn('No IMAP credentials found');
            Log::warning('Email dispatcher: No IMAP credentials found');
            return;
        }

        $dispatchedCount = 0;
        $timestamp = now()->format('Y-m-d H:i:s');

        foreach ($credentials as $credential) {
            try {
                FetchEmailsForProfileJob::dispatch($credential);
                $dispatchedCount++;
            } catch (\Exception $e) {
                $this->error(
                    "Failed to dispatch job for profile {$credential->profile_id}: " . $e->getMessage()
                );
                Log::error(
                    "Failed to dispatch email fetch job for profile {$credential->profile_id}: " . $e->getMessage(),
                    ['exception' => $e]
                );
            }
        }

        $this->line(
            "<info>[{$timestamp}]</info> Dispatched <fg=green>{$dispatchedCount}</> jobs for <fg=green>{$credentials->count()}</> credential(s)"
        );

        Log::info("Email dispatcher dispatched {$dispatchedCount} jobs for {$credentials->count()} credentials");
    }
}
