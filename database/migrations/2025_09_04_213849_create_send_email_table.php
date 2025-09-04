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
        Schema::create('sender_email', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('email')->unique();
            $table->string('name')->nullable();
            $table->string('image_path')->nullable();
            $table->string('top_level_domain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sender_email');
    }
};
