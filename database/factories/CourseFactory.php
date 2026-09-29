<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Course> */
class CourseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_code' => strtoupper(fake()->unique()->bothify('IT###')),
            'title' => fake()->randomElement(['Programming Fundamentals', 'Data Structures and Algorithms', 'Database Systems', 'Web Application Development', 'Computer Networks', 'Information Assurance', 'Human Computer Interaction', 'Systems Analysis and Design', 'Cloud Computing', 'Mobile Application Development', 'Software Engineering', 'Discrete Mathematics']),
            'description' => fake()->sentence(12),
            'credits' => 3,
            'lecture_hours' => 3,
            'lab_hours' => fake()->randomElement([0, 2, 3]),
            'program_id' => Program::factory(),
            'status' => 'active',
        ];
    }
}
