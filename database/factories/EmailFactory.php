<?php

namespace Database\Factories;

use App\Models\Email;
use App\Models\Profile;
use App\Models\Folder;
use App\Models\ImapCredentials;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailFactory extends Factory
{
    protected $model = Email::class;

    public function definition(): array
    {
        return [
            'profile_id' => 1,  // Use a default value
            'credential_id' => 1, // Use a default value
            'folder_id' => 1,  // Use a default value
            'subject' => $this->faker->sentence(),
            'from' => $this->faker->name(),
            'to' => $this->faker->email(),
            'sent_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'has_read' => $this->faker->boolean(),
            'uid' => $this->faker->unique()->numberBetween(1000, 99999),
            'html_body' => $this->faker->paragraphs(3, true),
            'is_archived' => false,
            'is_starred' => false,
            'is_deleted' => false,
        ];
    }
}