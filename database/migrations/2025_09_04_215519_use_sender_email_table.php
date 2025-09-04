<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Email;
use App\Models\IncomingEmailSender;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Loop over all emails and for each unique sender_email, create or find an IncomingEmailSender and store the related image
        $emails = Email::all();

        foreach ($emails as $email) {
            $senderEmail = $email->sender_email;
            if ($senderEmail) {
                $incomingEmailSender = IncomingEmailSender::firstOrCreate(
                    ['email' => $senderEmail],
                    [
                        'name' => $email->from ?? $senderEmail,
                        'top_level_domain' => IncomingEmailSender::email_to_domain($senderEmail),
                    ]
                );

                // Update the email to reference the sender_id
                $email->sender_id = $incomingEmailSender->id;
                $email->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
