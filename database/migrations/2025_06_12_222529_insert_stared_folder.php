<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
Use App\Models\Folder;
use App\Models\ImapCredentials;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $imapCredentials = ImapCredentials::all();
        foreach ($imapCredentials as $credential) {
            // Create the "Stared" folder for each credential
            Folder::create([
                'name' => 'Stared',
                'path' => 'stared',
                'imap_credential_id' => $credential->id,
            ]);
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
