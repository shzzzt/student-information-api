<?php

namespace Database\Factories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Program> */
class ProgramFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('BS??')),
            'name' => fake()->randomElement(['Information Technology', 'Computer Science', 'Information Systems']),
            'department' => 'College of Computing Studies',
            'degree_level' => 'bachelor',
            'total_credits' => fake()->numberBetween(120, 144),
            'status' => 'active',
        ];
    }
}
