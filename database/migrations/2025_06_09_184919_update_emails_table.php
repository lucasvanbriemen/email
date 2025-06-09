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
            //
            // Rename 'user_id' to 'credential_id'
            $table->renameColumn('user_id', 'credential_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('emails', function (Blueprint $table) {
            //
            // Rename 'credential_id' back to 'user_id'
            $table->renameColumn('credential_id', 'user_id');
        });
    }
};
