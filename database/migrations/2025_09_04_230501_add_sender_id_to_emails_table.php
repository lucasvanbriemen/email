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
        Schema::table('emails', function (Blueprint $table) {
            // Add nullable sender_id that references the sender_email table
            if (!Schema::hasColumn('emails', 'sender_id')) {
                $table->foreignId('sender_id')->nullable()->after('id');
            }
        });

        // Add the foreign key constraint in a separate call to avoid issues if column already exists
        Schema::table('emails', function (Blueprint $table) {
            // Only add the constraint if the column exists and there's no existing FK
            // Laravel doesn't provide a direct way to check FK existence here; rely on idempotence in test envs
            try {
                $table->foreign('sender_id')->references('id')->on('sender_email')->nullOnDelete();
            } catch (\Throwable $e) {
                // Ignore if the foreign key already exists or fails in some environments
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            try {
                $table->dropForeign(['sender_id']);
            } catch (\Throwable $e) {
                // Ignore if it doesn't exist
            }
            if (Schema::hasColumn('emails', 'sender_id')) {
                $table->dropColumn('sender_id');
            }
        });
    }
};

