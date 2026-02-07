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
        // Add FULLTEXT indexes for fast text search
        DB::statement('ALTER TABLE emails ADD FULLTEXT INDEX ft_subject (subject)');
        DB::statement('ALTER TABLE emails ADD FULLTEXT INDEX ft_html_body (html_body)');
        DB::statement('ALTER TABLE emails ADD FULLTEXT INDEX ft_both (subject, html_body)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop FULLTEXT indexes
        DB::statement('ALTER TABLE emails DROP INDEX ft_subject');
        DB::statement('ALTER TABLE emails DROP INDEX ft_html_body');
        DB::statement('ALTER TABLE emails DROP INDEX ft_both');
    }
};
