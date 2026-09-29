<?php

namespace Database\Factories;

use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Student> */
class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'student_number' => fake()->unique()->numerify('2026-####'),
            'first_name' => fake()->randomElement(['Juan', 'Maria', 'Jose', 'Ana', 'Paolo', 'Angelica', 'Miguel', 'Sofia', 'Carlo', 'Bianca', 'Rafael', 'Camille', 'Lorenzo', 'Isabella', 'Marco', 'Patricia']),
            'middle_name' => fake()->optional(0.7)->randomElement(['Santos', 'Reyes', 'Cruz', 'Garcia', 'Lim']),
            'last_name' => fake()->randomElement(['Dela Cruz', 'Santos', 'Reyes', 'Garcia', 'Mendoza', 'Torres', 'Flores', 'Ramos', 'Bautista', 'Aquino', 'Castillo', 'Navarro', 'Villanueva', 'Domingo']),
            'program_id' => Program::factory(),
            'year_level' => fake()->numberBetween(1, 4),
            'date_of_birth' => fake()->dateTimeBetween('-24 years', '-17 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(['male', 'female', 'prefer_not_to_say']),
            'phone' => fake()->numerify('09#########'),
            'address' => fake()->randomElement(['Quezon City, Metro Manila', 'Cebu City, Cebu', 'Davao City, Davao del Sur', 'Baguio City, Benguet', 'Iloilo City, Iloilo', 'Batangas City, Batangas']),
            'admission_date' => fake()->dateTimeBetween('-4 years', '-6 months')->format('Y-m-d'),
            'expected_graduation_date' => fake()->dateTimeBetween('+1 year', '+4 years')->format('Y-m-d'),
            'status' => 'active',
            'cumulative_gpa' => fake()->randomFloat(2, 1.75, 4),
        ];
    }
}
