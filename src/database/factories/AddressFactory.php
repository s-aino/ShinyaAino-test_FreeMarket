<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'postal' => substr(fake()->postcode(), 0, 8),
            'line1' => $this->faker->city() . $this->faker->streetAddress(),
            'line2' => $this->faker->secondaryAddress(),
            'is_temporary' => false,
        ];
    }
}
