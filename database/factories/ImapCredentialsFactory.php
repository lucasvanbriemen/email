<?php

namespace Database\Factories;

use App\Models\ImapCredentials;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ImapCredentialsFactory extends Factory
{
    protected $model = ImapCredentials::class;

    public function definition(): array
    {
        return [
            'profile_id' => Profile::factory(),
            'host' => $this->faker->domainName(),
            'port' => $this->faker->randomElement([143, 993]),
            'username' => $this->faker->userName(),
            'password' => $this->faker->password(),
            'encryption' => $this->faker->randomElement(['ssl', 'tls', null]),
        ];
    }
}