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
            // Add indexes for commonly searched/filtered columns
            $table->index('sent_at');
            $table->index('has_read');
            $table->index('sender_id');
            $table->index('subject');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropIndex(['sent_at']);
            $table->dropIndex(['has_read']);
            $table->dropIndex(['sender_id']);
            $table->dropIndex(['subject']);
        });
    }
};
