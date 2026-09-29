<?php

namespace Database\Factories;

use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Enrollment> */
class EnrollmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'course_offering_id' => CourseOffering::factory(),
            'enrolled_on' => fake()->dateTimeBetween('-5 months', '-1 month')->format('Y-m-d'),
            'dropped_on' => null,
            'status' => 'enrolled',
            'grade_mode' => 'graded',
        ];
    }
}
