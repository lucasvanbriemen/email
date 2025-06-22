<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Profiles;
use App\Models\Folder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        // Schema::table('folders', function (Blueprint $table) {
        //     // Add profile_id to folders table
        //     $table->foreignId('profile_id')->constrained('profiles')->onDelete('cascade');

        //     // Remove user_id from folders table
        //     $table->dropForeign(['imap_credential_id']);
        //     $table->dropColumn('imap_credential_id');
        // });

        // // set the profile_id to the profile that has the same username as the imap credential username
        // Folder::all()->each(function ($folder) {
        //     $profile = Profiles::where('email', $folder->imap_credential->username)->first();

        //     if ($profile) {
        //         $folder->profile_id = $profile->id;
        //         $folder->save();
        //     }
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
