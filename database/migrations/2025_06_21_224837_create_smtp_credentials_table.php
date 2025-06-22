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
        Schema::create('smtp_credentials', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('imap_credential_id');
            $table->string('host');
            $table->integer('port')->default(587);
            $table->string('username');
            $table->string('password');

            $table->string('reply_to_name')->nullable();
            $table->string('reply_to_email')->nullable();

            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smtp_credentials');
    }
};
