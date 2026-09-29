<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::create([
            'first_name' => 'API',
            'last_name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->actingAs($this->user, 'api');
    }

    public function test_program_crud_supports_search_filter_sort_and_pagination(): void
    {
        $program = $this->postJson('/api/v1/programs', [
            'code' => 'BSCS',
            'name' => 'Computer Science',
            'department' => 'Computing',
            'degree_level' => 'bachelor',
            'total_credits' => 120,
            'status' => 'active',
        ])->assertCreated()->json('data');

        $this->getJson('/api/v1/programs?search=computer&status=active&sort=code&per_page=20')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.data.0.code', 'BSCS');

        $this->patchJson('/api/v1/programs/'.$program['id'], ['name' => 'Computer Science Updated'])
            ->assertOk()
            ->assertJsonPath('data.name', 'Computer Science Updated');

        $this->deleteJson('/api/v1/programs/'.$program['id'])
            ->assertOk()
            ->assertJsonPath('data', null);
    }

    public function test_student_crud_supports_filters_and_deactivation(): void
    {
        $program = \App\Models\Program::create([
            'code' => 'BSIT',
            'name' => 'Information Technology',
            'degree_level' => 'bachelor',
            'status' => 'active',
        ]);

        $student = $this->postJson('/api/v1/students', [
            'user_id' => $this->user->id,
            'student_number' => '2026-0001',
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'jane.doe@example.com',
            'program_id' => $program->id,
            'year_level' => 2,
            'admission_date' => '2026-08-01',
            'status' => 'active',
        ])->assertCreated()->json('data');

        $this->assertSame('jane.doe@example.com', $this->user->refresh()->email);

        $this->getJson('/api/v1/students?search=jane&program_id='.$program->id.'&year_level=2&status=active')
            ->assertOk()
            ->assertJsonPath('data.0.student_number', '2026-0001')
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.per_page', 20);

        $this->patchJson('/api/v1/students/'.$student['id'], ['first_name' => 'Janet'])
            ->assertOk()
            ->assertJsonPath('data.first_name', 'Janet');

        $this->deleteJson('/api/v1/students/'.$student['id'])
            ->assertOk()
            ->assertJsonPath('data.status', 'inactive');
    }

    public function test_student_validation_rejects_duplicate_number_and_invalid_email(): void
    {
        $program = \App\Models\Program::create([
            'code' => 'BSSE',
            'name' => 'Software Engineering',
            'degree_level' => 'bachelor',
            'status' => 'active',
        ]);
        $firstUser = User::create([
            'first_name' => 'First',
            'last_name' => 'Student',
            'email' => 'first@example.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'status' => 'active',
        ]);
        $secondUser = User::create([
            'first_name' => 'Second',
            'last_name' => 'Student',
            'email' => 'second@example.com',
            'password' => bcrypt('password'),
            'role' => 'student',
            'status' => 'active',
        ]);

        $payload = [
            'user_id' => $firstUser->id,
            'student_number' => '2026-0099',
            'first_name' => 'First',
            'last_name' => 'Student',
            'program_id' => $program->id,
            'year_level' => 1,
            'admission_date' => '2026-08-01',
            'status' => 'active',
        ];
        $this->postJson('/api/v1/students', $payload)->assertCreated();

        $this->postJson('/api/v1/students', [
            ...$payload,
            'user_id' => $secondUser->id,
            'email' => 'not-an-email',
        ])->assertUnprocessable()
            ->assertJsonStructure(['success', 'errors' => ['student_number', 'email']]);

        $this->getJson('/api/v1/students/999999')
            ->assertNotFound();
    }

    public function test_course_and_academic_term_crud_validate_foreign_keys(): void
    {
        $program = \App\Models\Program::create([
            'code' => 'BSA',
            'name' => 'Accountancy',
            'degree_level' => 'bachelor',
            'status' => 'active',
        ]);

        $course = $this->postJson('/api/v1/courses', [
            'course_code' => 'ACC101',
            'title' => 'Financial Accounting',
            'credits' => 3,
            'lecture_hours' => 3,
            'lab_hours' => 0,
            'program_id' => $program->id,
            'status' => 'active',
        ])->assertCreated()->json('data');

        $this->getJson('/api/v1/courses?program_id='.$program->id.'&search=accounting')
            ->assertOk()
            ->assertJsonPath('data.data.0.course_code', 'ACC101');

        $term = $this->postJson('/api/v1/academic-terms', [
            'code' => '2026-FALL',
            'name' => 'Fall 2026',
            'term_type' => 'fall',
            'academic_year' => 2026,
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-15',
            'status' => 'planned',
        ])->assertCreated()->json('data');

        $this->patchJson('/api/v1/courses/'.$course['id'], ['title' => 'Updated Accounting'])
            ->assertOk();
        $this->patchJson('/api/v1/academic-terms/'.$term['id'], ['status' => 'open'])
            ->assertOk()
            ->assertJsonPath('data.status', 'open');

        $this->postJson('/api/v1/courses', [
            'course_code' => 'BAD101',
            'title' => 'Bad Course',
            'program_id' => 999999,
            'status' => 'active',
        ])->assertUnprocessable()
            ->assertJsonPath('success', false)
            ->assertJsonStructure(['errors' => ['program_id']]);
    }
}
