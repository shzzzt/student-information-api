<?php

namespace Tests\Feature;

use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Grade;
use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_cannot_change_url_id_to_read_another_students_data(): void
    {
        [$student, $otherStudent, $offering] = $this->createAcademicFixtures();
        $enrollment = Enrollment::create([
            'student_id' => $otherStudent->id,
            'course_offering_id' => $offering->id,
            'enrolled_on' => '2026-08-01',
            'status' => 'enrolled',
            'grade_mode' => 'graded',
        ]);
        $grade = Grade::create([
            'enrollment_id' => $enrollment->id,
            'numeric_grade' => 90,
            'status' => 'submitted',
        ]);

        $this->actingAs($student->user, 'api');

        $this->getJson('/api/v1/students/'.$otherStudent->id)
            ->assertForbidden();
        $this->getJson('/api/v1/students/'.$otherStudent->id.'/enrollments')
            ->assertForbidden();
        $this->getJson('/api/v1/students/'.$otherStudent->id.'/grades')
            ->assertForbidden();
        $this->getJson('/api/v1/students/'.$otherStudent->id.'/academic-record')
            ->assertForbidden();
        $this->getJson('/api/v1/enrollments/'.$enrollment->id)
            ->assertForbidden();
        $this->getJson('/api/v1/grades/'.$grade->id)
            ->assertForbidden();

        $this->getJson('/api/v1/students/'.$student->id)
            ->assertOk()
            ->assertJsonPath('data.id', $student->id);
        $this->getJson('/api/v1/students')
            ->assertForbidden();
        $this->getJson('/api/v1/grades')
            ->assertOk()
            ->assertJsonPath('data.data', []);
        $this->getJson('/api/v1/course-offerings')
            ->assertForbidden();
        $this->getJson('/api/v1/course-offerings/'.$offering->id.'/students')
            ->assertForbidden();
    }

    public function test_instructor_only_sees_assigned_offerings_and_cannot_manage_unassigned_grades(): void
    {
        [$student, , $offering] = $this->createAcademicFixtures();
        $otherInstructor = User::create([
            'first_name' => 'Other',
            'last_name' => 'Faculty',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
            'role' => 'faculty',
            'status' => 'active',
        ]);
        $otherOffering = $offering->replicate();
        $otherOffering->instructor_id = $otherInstructor->id;
        $otherOffering->section = '002';
        $otherOffering->save();
        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_offering_id' => $otherOffering->id,
            'enrolled_on' => '2026-08-01',
            'status' => 'enrolled',
            'grade_mode' => 'graded',
        ]);
        $grade = Grade::create([
            'enrollment_id' => $enrollment->id,
            'numeric_grade' => 80,
            'status' => 'submitted',
        ]);

        $this->actingAs($offering->instructor, 'api');

        $this->getJson('/api/v1/course-offerings')
            ->assertOk()
            ->assertJsonPath('data.data.0.id', $offering->id)
            ->assertJsonMissing(['id' => $otherOffering->id]);
        $this->getJson('/api/v1/course-offerings/'.$otherOffering->id)
            ->assertForbidden();
        $this->patchJson('/api/v1/grades/'.$grade->id, ['numeric_grade' => 85])
            ->assertForbidden();
    }

    public function test_faculty_cannot_view_enrollments_for_a_student_outside_their_offerings(): void
    {
        [$faculty, $outsideStudent] = $this->createOutsideStudentContext();

        $this->actingAs($faculty, 'api')
            ->getJson('/api/v1/students/'.$outsideStudent->id.'/enrollments')
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_faculty_cannot_view_grades_for_a_student_outside_their_offerings(): void
    {
        [$faculty, $outsideStudent] = $this->createOutsideStudentContext();

        $this->actingAs($faculty, 'api')
            ->getJson('/api/v1/students/'.$outsideStudent->id.'/grades')
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_faculty_cannot_view_academic_record_for_a_student_outside_their_offerings(): void
    {
        [$faculty, $outsideStudent] = $this->createOutsideStudentContext();

        $this->actingAs($faculty, 'api')
            ->getJson('/api/v1/students/'.$outsideStudent->id.'/academic-record')
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }

    public function test_faculty_can_view_nested_records_for_a_student_in_their_offering(): void
    {
        [$student, , $offering] = $this->createAcademicFixtures();
        Enrollment::create([
            'student_id' => $student->id,
            'course_offering_id' => $offering->id,
            'enrolled_on' => '2026-08-01',
            'status' => 'enrolled',
            'grade_mode' => 'graded',
        ]);

        $this->actingAs($offering->instructor, 'api');

        $this->getJson('/api/v1/students/'.$student->id.'/enrollments')->assertOk();
        $this->getJson('/api/v1/students/'.$student->id.'/grades')->assertOk();
        $this->getJson('/api/v1/students/'.$student->id.'/academic-record')->assertOk();
    }

    private function createOutsideStudentContext(): array
    {
        [, $outsideStudent, $assignedOffering] = $this->createAcademicFixtures();
        $otherFaculty = User::create([
            'first_name' => 'Other',
            'last_name' => 'Faculty',
            'email' => 'outside-faculty@example.com',
            'password' => bcrypt('password'),
            'role' => 'faculty',
            'status' => 'active',
        ]);
        $outsideOffering = $assignedOffering->replicate();
        $outsideOffering->instructor_id = $otherFaculty->id;
        $outsideOffering->section = '002';
        $outsideOffering->save();
        Enrollment::create([
            'student_id' => $outsideStudent->id,
            'course_offering_id' => $outsideOffering->id,
            'enrolled_on' => '2026-08-01',
            'status' => 'enrolled',
            'grade_mode' => 'graded',
        ]);

        return [$assignedOffering->instructor, $outsideStudent];
    }

    private function createAcademicFixtures(): array
    {
        $faculty = User::create([
            'first_name' => 'Assigned',
            'last_name' => 'Faculty',
            'email' => 'faculty@example.com',
            'password' => bcrypt('password'),
            'role' => 'faculty',
            'status' => 'active',
        ]);
        $studentUser = User::create([
            'first_name' => 'Own',
            'last_name' => 'Student',
            'email' => 'own@example.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'status' => 'active',
        ]);
        $otherUser = User::create([
            'first_name' => 'Other',
            'last_name' => 'Student',
            'email' => 'other-student@example.com',
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
            'title' => 'Computing',
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
        $student = Student::create([
            'user_id' => $studentUser->id,
            'student_number' => '2026-0001',
            'first_name' => 'Own',
            'last_name' => 'Student',
            'program_id' => $program->id,
            'year_level' => 1,
            'admission_date' => '2026-08-01',
            'status' => 'active',
        ]);
        $otherStudent = Student::create([
            'user_id' => $otherUser->id,
            'student_number' => '2026-0002',
            'first_name' => 'Other',
            'last_name' => 'Student',
            'program_id' => $program->id,
            'year_level' => 1,
            'admission_date' => '2026-08-01',
            'status' => 'active',
        ]);
        $offering = CourseOffering::create([
            'course_id' => $course->id,
            'academic_term_id' => $term->id,
            'instructor_id' => $faculty->id,
            'section' => '001',
            'delivery_mode' => 'in_person',
            'capacity' => 30,
            'status' => 'open',
        ]);

        return [$student, $otherStudent, $offering];
    }
}
