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
        //
        Schema::table('emails', function (Blueprint $table) {
            $table->string('sender_email')->nullable()->after('from');
            $table->string('to')->nullable()->after('sender_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn(['sender_email', 'to']);
        });
    }
};
