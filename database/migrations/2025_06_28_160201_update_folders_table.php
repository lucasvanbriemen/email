<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Folder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('folders', function (Blueprint $table) {
            //

            $folders = Folder::all();
            foreach ($folders as $folder) {
                $folder->icon = Folder::$defaultFolderIcons[$folder->path] ?? null;
                $folder->save();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('folders', function (Blueprint $table) {
            //

            $folders = Folder::all();
            foreach ($folders as $folder) {
                $folder->icon = null; // Reset icon to null
                $folder->save();
            }
        });
    }
};
