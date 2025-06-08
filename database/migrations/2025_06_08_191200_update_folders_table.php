<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('folders', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->foreign('imap_credential_id')
                  ->references('id')->on('imap_credentials')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('folders', function (Blueprint $table) {
            $table->dropForeign(['imap_credential_id']);
            $table->foreign('imap_credential_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }
};
