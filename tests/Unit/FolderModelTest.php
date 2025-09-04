<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Folder;

class FolderModelTest extends TestCase
{
    public function test_folder_model_exists()
    {
        $folder = new Folder();
        $this->assertInstanceOf(Folder::class, $folder);
    }

    public function test_folder_has_default_folders()
    {
        $this->assertIsArray(Folder::$defaultFolders);
        $this->assertArrayHasKey('inbox', Folder::$defaultFolders);
        $this->assertArrayHasKey('sent', Folder::$defaultFolders);
        $this->assertArrayHasKey('drafts', Folder::$defaultFolders);
    }
}