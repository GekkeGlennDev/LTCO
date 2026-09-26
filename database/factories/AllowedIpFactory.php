<?php

namespace Database\Factories;

use App\Models\AllowedIp;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AllowedIp>
 */
class AllowedIpFactory extends Factory
{
    public function definition(): array
    {
        return [
            'range' => fake()->unique()->ipv4(),
            'description' => fake()->optional()->sentence(3),
        ];
    }
}
