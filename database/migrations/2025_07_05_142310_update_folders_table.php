<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('folders', function (Blueprint $table) {
            //
            // If the imap_credential_id exists, drop it as foreign key and remove it afterwards
            if (Schema::hasColumn('folders', 'imap_credential_id')) {
                $table->dropForeign(['imap_credential_id']);
                $table->dropColumn('imap_credential_id');
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
