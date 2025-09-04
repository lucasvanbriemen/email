<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\IncomingEmailSender;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        $senders = IncomingEmailSender::all();
        foreach ($senders as $sender) {
            $dbPath = $sender->image_path;

            $pathPrefix = 'attachments/logos/';
            if (file_exists($pathPrefix . $dbPath)) {
                // File exists, nothing to do
                continue;
            }

            $sender->store_domain_as_logo();
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
