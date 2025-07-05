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
            // If the imap_credential_id exists, drop it as fornein key and remove it afterwards
            if (Schema::hasColumn('folders', 'imap_credential_id')) {
                // Add profile_id to folders
                $table->unsignedBigInteger('profile_id')->nullable()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('folders', function (Blueprint $table) {
        });
    }
};
