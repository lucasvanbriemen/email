<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('imap_credentials', function (Blueprint $table) {
            $table->timestamp('last_fetched_at')->nullable()->index();
            $table->text('last_fetch_error')->nullable();
            $table->integer('fetch_attempts')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('imap_credentials', function (Blueprint $table) {
            $table->dropColumn(['last_fetched_at', 'last_fetch_error', 'fetch_attempts']);
        });
    }
};
