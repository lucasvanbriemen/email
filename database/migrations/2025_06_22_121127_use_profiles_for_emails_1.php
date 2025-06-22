<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Profiles;
use App\Models\Email;
use App\Models\ImapCredentials;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //

        Schema::table('emails', function (Blueprint $table) {
            // Add profile_id to emails table
            $table->integer('profile_id');
        });

        Email::all()->each(function ($email) {
            // Find the profile associated with the email's imap_credential
            $imapCredential = ImapCredentials::where('id', $email->credential_id)->first();

            $profile = Profiles::where('id', $imapCredential->profile_id)->first();

            if ($profile) {
                $email->profile_id = $profile->id;
                $email->save();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
