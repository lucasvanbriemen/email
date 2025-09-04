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
        $processedEmails = [];
        foreach ($emails as $email) {
            if (!in_array($email->sender_email, $processedEmails)) {
                $processedEmails[] = $email->sender_email;
                $sender = IncomingEmailSender::firstOrCreate(['email' => $email->sender_email]);
                $email->sender()->associate($sender);
                $email->save();

                $sender->store_domain_as_logo();
            }
        }
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
