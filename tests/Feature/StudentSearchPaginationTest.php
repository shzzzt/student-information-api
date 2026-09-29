<?php

namespace Tests\Feature;

use App\Models\Program;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentSearchPaginationTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_index_searches_filters_sorts_and_returns_metadata(): void
    {
        $admin = User::create([
            'first_name' => 'API',
            'last_name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);
        $program = Program::create([
            'code' => 'BSIT',
            'name' => 'Information Technology',
            'degree_level' => 'bachelor',
            'status' => 'active',
        ]);

        foreach ([
            ['Dela Cruz', '2026-0001', 3],
            ['Dela Rosa', '2026-0002', 3],
            ['Santos', '2026-0003', 3],
        ] as $index => [$lastName, $number, $yearLevel]) {
            $user = User::create([
                'first_name' => 'Student'.$index,
                'last_name' => $lastName,
                'email' => 'student'.$index.'@example.com',
                'password' => bcrypt('password'),
                'role' => 'student',
                'status' => 'active',
            ]);
            Student::create([
                'user_id' => $user->id,
                'student_number' => $number,
                'first_name' => 'Student'.$index,
                'last_name' => $lastName,
                'program_id' => $program->id,
                'year_level' => $yearLevel,
                'admission_date' => '2026-08-01',
                'status' => 'active',
            ]);
        }

        $this->actingAs($admin, 'api');

        $this->getJson('/api/v1/students?search=dela&program_id='.$program->id.'&year_level=3&sort=-last_name&page=2&per_page=1')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.last_name', 'Dela Cruz')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.per_page', 1)
            ->assertJsonPath('meta.total', 2)
            ->assertJsonPath('meta.last_page', 2);

        $this->getJson('/api/v1/students?sort=first_name')
            ->assertUnprocessable()
            ->assertJsonStructure(['success', 'errors' => ['sort']]);

        $this->getJson('/api/v1/students?per_page=101')
            ->assertUnprocessable()
            ->assertJsonStructure(['success', 'errors' => ['per_page']]);
    }
}