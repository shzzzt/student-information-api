<?php

namespace Database\Factories;

use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CourseOffering> */
class CourseOfferingFactory extends Factory
{
    public function definition(): array
    {
        [$dayOfWeek, $startTime, $endTime] = fake()->randomElement([
            ['MWF', '08:00', '09:30'],
            ['MWF', '10:00', '11:30'],
            ['TTh', '09:00', '10:30'],
            ['TTh', '13:00', '14:30'],
            ['Sat', '08:00', '11:00'],
        ]);

        return [
            'course_id' => Course::factory(),
            'academic_term_id' => AcademicTerm::factory(),
            'instructor_id' => User::factory()->state(['role' => 'faculty']),
            'section' => fake()->numerify('0##'),
            'room' => fake()->randomElement(['CCS 201', 'CCS 202', 'Lab 1', 'Lab 2', 'Online']),
            'day_of_week' => $dayOfWeek,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'delivery_mode' => fake()->randomElement(['in_person', 'online', 'hybrid']),
            'capacity' => fake()->randomElement([30, 35, 40]),
            'enrolled_count' => 0,
            'status' => 'open',
        ];
    }
}
