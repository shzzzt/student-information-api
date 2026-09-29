<?php

namespace Database\Factories;

use App\Models\AcademicTerm;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<AcademicTerm> */
class AcademicTermFactory extends Factory
{
    public function definition(): array
    {
        $year = fake()->numberBetween(2025, 2030);
        $type = fake()->randomElement(['fall', 'spring']);

        return [
            'code' => $year.'-'.strtoupper($type),
            'name' => ucfirst($type).' '.$year,
            'term_type' => $type,
            'academic_year' => $year,
            'start_date' => $type === 'fall' ? "$year-08-01" : "$year-01-15",
            'end_date' => $type === 'fall' ? "$year-12-15" : "$year-05-31",
            'registration_start' => $type === 'fall' ? "$year-06-15" : ($year - 1).'-11-15',
            'registration_end' => $type === 'fall' ? "$year-07-31" : "$year-01-14",
            'status' => 'open',
        ];
    }
}
