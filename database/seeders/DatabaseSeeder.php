<?php

namespace Database\Seeders;

use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        DB::transaction(function (): void {
            $this->clearAcademicData();

            User::factory()->create([
                'first_name' => 'Andres', 'last_name' => 'Bonifacio',
                'email' => 'admin@example.com', 'role' => 'admin',
            ]);
            User::factory()->create([
                'first_name' => 'Melchora', 'last_name' => 'Aquino',
                'email' => 'registrar@example.com', 'role' => 'registrar',
            ]);
            $faculty = collect([
                User::factory()->create([
                    'first_name' => 'Jose', 'last_name' => 'Rizal',
                    'email' => 'faculty@example.com', 'role' => 'faculty',
                ]),
                User::factory()->create([
                    'first_name' => 'Fe', 'last_name' => 'Del Mundo',
                    'email' => 'faculty2@example.com', 'role' => 'faculty',
                ]),
            ]);

            $programs = Program::factory()->count(3)->sequence(
                ['code' => 'BSIT', 'name' => 'Bachelor of Science in Information Technology', 'total_credits' => 140],
                ['code' => 'BSCS', 'name' => 'Bachelor of Science in Computer Science', 'total_credits' => 142],
                ['code' => 'BSIS', 'name' => 'Bachelor of Science in Information Systems', 'total_credits' => 138],
            )->create(['department' => 'College of Computing Studies']);

            $students = collect(range(1, 100))->map(function (int $number) use ($programs): Student {
                $user = User::factory()->create([
                    'email' => $number === 1 ? 'student@example.com' : sprintf('student%03d@example.com', $number),
                    'role' => 'student',
                ]);

                return Student::factory()->create([
                    'user_id' => $user->id,
                    'student_number' => sprintf('2026-%04d', $number),
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'program_id' => $programs[($number - 1) % $programs->count()]->id,
                ]);
            });

            $courseTitles = [
                'Programming Fundamentals', 'Object-Oriented Programming', 'Data Structures and Algorithms',
                'Database Management Systems', 'Web Application Development', 'Mobile Application Development',
                'Computer Networks', 'Information Assurance and Security', 'Systems Analysis and Design',
                'Human Computer Interaction', 'Software Engineering', 'Cloud Computing', 'Discrete Mathematics',
                'Operating Systems', 'Computer Architecture', 'Data Analytics', 'Artificial Intelligence',
                'IT Project Management', 'Professional Ethics', 'Capstone Project',
            ];
            $courses = Course::factory()->count(20)->sequence(
                fn ($sequence) => [
                    'course_code' => sprintf('IT%03d', 101 + $sequence->index),
                    'title' => $courseTitles[$sequence->index],
                    'program_id' => $programs[$sequence->index % $programs->count()]->id,
                ],
            )->create();

            $terms = AcademicTerm::factory()->count(2)->sequence(
                [
                    'code' => '2026-FALL', 'name' => 'First Semester AY 2026-2027',
                    'term_type' => 'fall', 'academic_year' => 2026,
                    'start_date' => '2026-08-01', 'end_date' => '2026-12-15',
                    'registration_start' => '2026-06-15', 'registration_end' => '2026-07-31',
                    'status' => 'open',
                ],
                [
                    'code' => '2027-SPRING', 'name' => 'Second Semester AY 2026-2027',
                    'term_type' => 'spring', 'academic_year' => 2027,
                    'start_date' => '2027-01-15', 'end_date' => '2027-05-31',
                    'registration_start' => '2026-11-15', 'registration_end' => '2027-01-14',
                    'status' => 'planned',
                ],
            )->create();

            $offerings = $courses->values()->map(fn (Course $course, int $index) => CourseOffering::factory()->create([
                'course_id' => $course->id,
                'academic_term_id' => $terms[$index % $terms->count()]->id,
                'instructor_id' => $faculty[$index % $faculty->count()]->id,
                'section' => '001',
            ]));

            $enrollments = $students->crossJoin($offerings)->shuffle()->take(200)
                ->map(fn (array $pair) => Enrollment::factory()->create([
                    'student_id' => $pair[0]->id,
                    'course_offering_id' => $pair[1]->id,
                ]));

            $enrollments->shuffle()->take(100)->each(function (Enrollment $enrollment): void {
                Grade::factory()->create([
                    'enrollment_id' => $enrollment->id,
                    'graded_by' => $enrollment->courseOffering->instructor_id,
                ]);
            });

            $offerings->each(fn (CourseOffering $offering) => $offering->update([
                'enrolled_count' => $offering->enrollments()->count(),
            ]));
        });
    }

    private function clearAcademicData(): void
    {
        foreach (['grades', 'enrollments', 'course_offerings', 'students', 'courses', 'academic_terms', 'programs', 'users'] as $table) {
            DB::table($table)->delete();
        }
    }
}
