<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\ImapCredentials;
use App\Models\Profiles; 

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        // For every ImapCredential, create a profile
        ImapCredentials::all()->each(function ($imapCredential) {

            // linked profile count is how many profiles are linked to this user id
            $linkedProfileCount = Profiles::where('user_id', $imapCredential->user_id)->count() + 1; // Increment by 1 for the new profile


            Profiles::create([
                'user_id' => $imapCredential->user_id,
                'name' => $imapCredential->username, // Assuming username is used as profile name
                'email' => $imapCredential->username,
                'linked_profile_count' => $linkedProfileCount, // Default to 1, can be adjusted later
            ]);
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
