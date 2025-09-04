<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;

return new class extends Migration
{
    public function up(): void
    {
        $path = storage_path('app/attachment/logo');

        if (File::exists($path)) {
            File::deleteDirectory($path, true); // deletes directory and contents
            File::makeDirectory($path); // recreate empty directory
        }
    }
};
