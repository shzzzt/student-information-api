<?php

namespace Database\Factories;

use App\Models\Enrollment;
use App\Models\Grade;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Grade> */
class GradeFactory extends Factory
{
    public function definition(): array
    {
        $numericGrade = fake()->randomFloat(2, 75, 99);
        $letterGrade = match (true) {
            $numericGrade >= 95 => 'A',
            $numericGrade >= 90 => 'A-',
            $numericGrade >= 85 => 'B+',
            $numericGrade >= 80 => 'B',
            default => 'C',
        };

        return [
            'enrollment_id' => Enrollment::factory(),
            'letter_grade' => $letterGrade,
            'numeric_grade' => $numericGrade,
            'grade_points' => match ($letterGrade) {
                'A' => 4.0, 'A-' => 3.7, 'B+' => 3.3, 'B' => 3.0, default => 2.0,
            },
            'status' => 'submitted',
            'graded_by' => null,
            'graded_at' => now(),
            'remarks' => fake()->optional(0.25)->sentence(),
        ];
    }
}
