<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Profiles;
use App\Models\SmtpCredentials;
use App\Models\ImapCredentials;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::table('smtp_credentials', function (Blueprint $table) {
            $table->integer('profile_id')->nullable()->after('id');
        });

        SmtpCredentials::all()->each(function ($smtp) {
            $imap = ImapCredentials::where('id', $smtp->imap_credential_id)
                ->first();

            $smtp->profile_id = $imap->profile_id;
            $smtp->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('smtp_credentials', function (Blueprint $table) {
            $table->dropColumn('profile_id');
        });
    }
};
