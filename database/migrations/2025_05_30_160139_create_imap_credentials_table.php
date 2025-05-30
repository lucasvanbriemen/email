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
        Schema::create('imap_credentials', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('host');
            $table->integer('port')->default(993);
            $table->string('protocol')->default('imap'); // might also use imap, [pop3 or nntp (untested)]
            $table->string('encryption')->default('ssl'); // Supported: false, 'ssl', 'tls', 'notls', 'starttls'

            $table->boolean('validate_cert')->default(true);
            $table->string('username');
            $table->string('password');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imap_credentials');
    }
};
