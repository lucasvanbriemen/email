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
        Schema::create('remote_folders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('imap_credential_id')
                ->constrained('imap_credentials')
                ->onDelete('cascade');

            $table->string('path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_folders');
    }
};
