<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $firstName = fake()->randomElement(['Juan', 'Maria', 'Jose', 'Ana', 'Paolo', 'Angelica', 'Miguel', 'Sofia', 'Carlo', 'Bianca', 'Rafael', 'Camille', 'Lorenzo', 'Isabella', 'Marco', 'Patricia']);
        $lastName = fake()->randomElement(['Dela Cruz', 'Santos', 'Reyes', 'Garcia', 'Mendoza', 'Torres', 'Flores', 'Ramos', 'Bautista', 'Aquino', 'Castillo', 'Navarro', 'Villanueva', 'Domingo']);

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => fake()->unique()->userName().'@example.com',
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'student',
            'status' => 'active',
            'remember_token' => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => ['email_verified_at' => null]);
    }
}
