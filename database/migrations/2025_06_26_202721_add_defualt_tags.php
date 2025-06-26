<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Tag;
use App\Models\Profile;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $profiles = Profile::all();
        foreach ($profiles as $profile) {
            foreach (Tag::$defaultTags as $tag) {
                DB::table('tags')->insert([
                    'profile_id' => $profile->id,
                    'name' => $tag['name'],
                    'color' => $tag['color'],
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('tags', function (Blueprint $table) {
            // empty down migration for tags
            $table->whereIn('name', array_column(Tag::$defaultTags, 'name'))->delete();
        });
    }
};
