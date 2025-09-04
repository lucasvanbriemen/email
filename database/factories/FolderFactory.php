<?php

namespace Database\Factories;

use App\Models\Folder;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class FolderFactory extends Factory
{
    protected $model = Folder::class;

    public function definition(): array
    {
        $folderName = $this->faker->randomElement(['inbox', 'sent', 'drafts', 'spam', 'trash', 'archive']);
        
        return [
            'profile_id' => Profile::factory(),
            'name' => ucfirst($folderName),
            'path' => $folderName,
        ];
    }
}