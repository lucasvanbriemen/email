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
            $table->boolean('is_archived')->default(false)->after('folder_id');
            $table->boolean('is_starred')->default(false)->after('is_archived');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn(['is_archived', 'is_starred']);
        });
    }
};
