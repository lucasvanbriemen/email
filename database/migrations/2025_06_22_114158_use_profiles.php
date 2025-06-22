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
        Schema::table('imap_credentials', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
            $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade');
        });

        // set the profile_id to the profile that has the same username as the imap credential username
        ImapCredentials::all()->each(function ($imapCredential) {
            $profile = Profiles::where('email', $imapCredential->username)->first();

            if ($profile) {
                $imapCredential->profile_id = $profile->id;
                $imapCredential->save();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('imap_credentials', function (Blueprint $table) {
            $table->dropForeign(['profile_id']);
            $table->dropColumn('profile_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        });
    }
};
