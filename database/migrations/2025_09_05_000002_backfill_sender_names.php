<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Email;
use App\Models\IncomingEmailSender;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Backfill sender_name for existing emails using the name from IncomingEmailSender
        Email::whereNull('sender_name')
            ->whereNotNull('sender_id')
            ->chunkById(100, function ($emails) {
                foreach ($emails as $email) {
                    $sender = IncomingEmailSender::find($email->sender_id);
                    if ($sender) {
                        $email->sender_name = $sender->name;
                        $email->save();
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Set all sender_name fields back to null
        Email::whereNotNull('sender_name')->update(['sender_name' => null]);
    }
};