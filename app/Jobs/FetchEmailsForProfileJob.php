<?php

namespace App\Jobs;

use App\Models\ImapCredentials;
use App\Services\EmailFetchingService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class FetchEmailsForProfileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;
    public $backoff = [60, 300, 900]; // 1 min, 5 min, 15 min

    /**
     * The ImapCredentials instance
     */
    protected ImapCredentials $credential;

    /**
     * Create a new job instance.
     */
    public function __construct(ImapCredentials $credential)
    {
        $this->credential = $credential;
    }

    /**
     * Execute the job.
     */
    public function handle(EmailFetchingService $service): void
    {
        try {
            $credential = ImapCredentials::findOrFail($this->credential->id);

            // Update fetch timestamp and attempt count
            $credential->update([
                'last_fetched_at' => now(),
                'fetch_attempts' => $credential->fetch_attempts + 1,
                'last_fetch_error' => null,
            ]);

            Log::info("Starting email fetch for profile {$credential->profile_id}");

            // Connect to IMAP server
            $client = $service->connectToImap($credential);

            // Fetch messages from INBOX
            $messages = $service->fetchInboxMessages($client);

            if ($messages->isEmpty()) {
                Log::info("No messages found for profile {$credential->profile_id}");
                $service->updateSystemLastFetchedAt();
                return;
            }

            // Get INBOX folder ID
            $folderId = $service->getInboxFolderId($credential->profile_id);

            if (!$folderId) {
                Log::warning("INBOX folder not found for profile {$credential->profile_id}");
                $service->updateSystemLastFetchedAt();
                return;
            }

            // Process each message
            $processedCount = 0;
            foreach ($messages as $message) {
                try {
                    $email = $service->processMessage($message, $credential, $folderId);
                    if ($email) {
                        $processedCount++;
                    }
                } catch (Exception $e) {
                    Log::error(
                        "Failed to process message for profile {$credential->profile_id}: " . $e->getMessage(),
                        ['exception' => $e]
                    );
                }
            }

            Log::info("Processed {$processedCount} emails for profile {$credential->profile_id}");

            // Update system last fetched timestamp
            $service->updateSystemLastFetchedAt();

        } catch (Exception $e) {
            Log::error(
                "Error fetching emails for profile {$this->credential->profile_id}: " . $e->getMessage(),
                ['exception' => $e]
            );

            // Update credential with error
            try {
                $this->credential->update([
                    'last_fetch_error' => substr($e->getMessage(), 0, 255),
                ]);
            } catch (Exception $updateError) {
                Log::error("Failed to update credential error status: " . $updateError->getMessage());
            }

            // Re-throw to trigger retry
            throw $e;
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(Throwable $exception): void
    {
        $credentialId = $this->credential->id;
        $profileId = $this->credential->profile_id;

        Log::error(
            "Email fetch job failed for profile {$profileId} (credential {$credentialId}): " . $exception->getMessage(),
            [
                'exception' => $exception,
                'credential_id' => $credentialId,
                'profile_id' => $profileId,
                'job_attempts' => $this->attempts(),
            ]
        );

        // Update credential with final error
        try {
            $credential = ImapCredentials::find($credentialId);
            if ($credential) {
                $credential->update([
                    'last_fetch_error' => 'Job failed after ' . $this->attempts() . ' attempts: ' . substr($exception->getMessage(), 0, 200),
                ]);
            }
        } catch (Exception $e) {
            Log::error("Failed to update credential on job failure: " . $e->getMessage());
        }
    }

    /**
     * Determine the time at which the job should timeout.
     */
    public function retryUntil(): \DateTime
    {
        return now()->addHours(1);
    }
}
