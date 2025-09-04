<?php

namespace Database\Factories;

use App\Models\SmtpCredentials;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class SmtpCredentialsFactory extends Factory
{
    protected $model = SmtpCredentials::class;

    public function definition(): array
    {
        return [
            'profile_id' => Profile::factory(),
            'username' => $this->faker->userName(),
            'password' => $this->faker->password(),
            'from_email' => $this->faker->safeEmail(),
            'from_name' => $this->faker->name(),
            'host' => $this->faker->domainName(),
            'port' => $this->faker->randomElement([25, 465, 587]),
        ];
    }
}