<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Folder;
use App\Models\Profile;
use App\Models\ImapCredentials;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('folders', function (Blueprint $table) {
            //
            // If the imap_credential_id exists, 
            if (Schema::hasColumn('folders', 'imap_credential_id')) {
                // Update all the folders -> profile id to the profile id that imap_credential_id has
                $folders = Folder::all();

                foreach ($folders as $folder) {
                    $imapID = $folder->imap_credential_id;

                    $profileid = ImapCredentials::where('id', $imapID)
                        ->value('profile_id');

                    $folder->profile_id = $profileid;
                    $folder->save();
                }
            }

            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('folders', function (Blueprint $table) {
            //
        });
    }
};
