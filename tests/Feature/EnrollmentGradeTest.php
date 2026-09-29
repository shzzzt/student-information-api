<?php

namespace Tests\Feature;

use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentGradeTest extends TestCase
{
    use RefreshDatabase;

    private User $faculty;
    private User $registrar;
    private User $studentUser;
    private Student $student;
    private CourseOffering $offering;

    protected function setUp(): void
    {
        parent::setUp();

        $this->faculty = User::create([
            'first_name' => 'Assigned',
            'last_name' => 'Faculty',
            'email' => 'faculty@example.com',
            'password' => bcrypt('password'),
            'role' => 'faculty',
            'status' => 'active',
        ]);
        $this->registrar = User::create([
            'first_name' => 'Registrar',
            'last_name' => 'Manager',
            'email' => 'registrar@example.com',
            'password' => bcrypt('password'),
            'role' => 'registrar',
            'status' => 'active',
        ]);
        $this->studentUser = User::create([
            'first_name' => 'Enrolled',
            'last_name' => 'Student',
            'email' => 'student@example.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'status' => 'active',
        ]);

        $program = Program::create([
            'code' => 'BSCS',
            'name' => 'Computer Science',
            'degree_level' => 'bachelor',
            'status' => 'active',
        ]);
        $course = Course::create([
            'course_code' => 'CS101',
            'title' => 'Introduction to Computing',
            'program_id' => $program->id,
            'status' => 'active',
        ]);
        $term = AcademicTerm::create([
            'code' => '2026-FALL',
            'name' => 'Fall 2026',
            'term_type' => 'fall',
            'academic_year' => 2026,
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-15',
            'status' => 'open',
        ]);
        $this->student = Student::create([
            'user_id' => $this->studentUser->id,
            'student_number' => '2026-0001',
            'first_name' => 'Enrolled',
            'last_name' => 'Student',
            'program_id' => $program->id,
            'year_level' => 1,
            'admission_date' => '2026-08-01',
            'status' => 'active',
        ]);
        $this->offering = CourseOffering::create([
            'course_id' => $course->id,
            'academic_term_id' => $term->id,
            'instructor_id' => $this->faculty->id,
            'section' => '001',
            'delivery_mode' => 'in_person',
            'capacity' => 30,
            'status' => 'open',
        ]);

        $this->actingAs($this->registrar, 'api');
    }

    public function test_duplicate_enrollment_returns_conflict_and_nested_lists_work(): void
    {
        $payload = [
            'student_id' => $this->student->id,
            'course_offering_id' => $this->offering->id,
            'enrolled_on' => '2026-08-01',
            'status' => 'enrolled',
            'grade_mode' => 'graded',
        ];

        $enrollment = $this->postJson('/api/v1/enrollments', $payload)
            ->assertCreated()
            ->json('data');

        $this->postJson('/api/v1/enrollments', $payload)
            ->assertStatus(409)
            ->assertJsonPath('success', false);

        $this->getJson('/api/v1/students/'.$this->student->id.'/enrollments')
            ->assertOk()
            ->assertJsonPath('data.0.id', $enrollment['id']);

        $this->getJson('/api/v1/course-offerings/'.$this->offering->id.'/students')
            ->assertOk()
            ->assertJsonPath('data.0.id', $this->student->id);

        $this->postJson('/api/v1/enrollments', [
            ...$payload,
            'student_id' => 999999,
        ])->assertUnprocessable()
            ->assertJsonStructure(['success', 'errors' => ['student_id']]);
    }

    public function test_only_assigned_faculty_can_manage_grades_and_records_group_by_term(): void
    {
        $enrollment = $this->postJson('/api/v1/enrollments', [
            'student_id' => $this->student->id,
            'course_offering_id' => $this->offering->id,
            'enrolled_on' => '2026-08-01',
            'status' => 'enrolled',
            'grade_mode' => 'graded',
        ])->json('data');

        $otherFaculty = User::create([
            'first_name' => 'Other',
            'last_name' => 'Faculty',
            'email' => 'other-faculty@example.com',
            'password' => bcrypt('password'),
            'role' => 'faculty',
            'status' => 'active',
        ]);
        $this->actingAs($otherFaculty, 'api');
        $this->postJson('/api/v1/grades', [
            'enrollment_id' => $enrollment['id'],
            'numeric_grade' => 95,
            'status' => 'submitted',
        ])->assertForbidden();

        $this->actingAs($this->faculty, 'api');
        $grade = $this->postJson('/api/v1/grades', [
            'enrollment_id' => $enrollment['id'],
            'numeric_grade' => 95,
            'status' => 'submitted',
        ])->assertCreated()->json('data');

        $this->actingAs($otherFaculty, 'api');
        $this->patchJson('/api/v1/grades/'.$grade['id'], ['numeric_grade' => 90])
            ->assertForbidden();

        $this->actingAs($this->faculty, 'api');
        $this->patchJson('/api/v1/grades/'.$grade['id'], ['numeric_grade' => 90])
            ->assertOk()
            ->assertJsonPath('data.numeric_grade', '90.00');

        $this->postJson('/api/v1/grades', [
            'enrollment_id' => $enrollment['id'],
            'numeric_grade' => 101,
            'status' => 'submitted',
        ])->assertUnprocessable()
            ->assertJsonStructure(['success', 'errors' => ['numeric_grade']]);

        $this->getJson('/api/v1/students/'.$this->student->id.'/grades')
            ->assertOk()
            ->assertJsonPath('data.0.id', $grade['id']);

        $this->getJson('/api/v1/students/'.$this->student->id.'/academic-record')
            ->assertOk()
            ->assertJsonPath('data.terms.0.enrollments.0.grade.id', $grade['id']);
    }
}
