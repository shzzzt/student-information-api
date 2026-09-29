<?php

namespace Tests\Feature;

use App\Models\AcademicTerm;
use App\Models\Course;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseOfferingScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_offering_accepts_and_returns_a_valid_schedule(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $faculty = User::factory()->create(['role' => 'faculty']);
        $program = Program::factory()->create();
        $course = Course::factory()->create(['program_id' => $program->id]);
        $term = AcademicTerm::factory()->create();

        $this->actingAs($admin, 'api')->postJson('/api/v1/course-offerings', [
            'course_id' => $course->id,
            'academic_term_id' => $term->id,
            'instructor_id' => $faculty->id,
            'section' => '001',
            'room' => 'CCS 201',
            'day_of_week' => 'MWF',
            'start_time' => '09:00',
            'end_time' => '10:30',
            'delivery_mode' => 'in_person',
            'capacity' => 30,
            'status' => 'open',
        ])->assertCreated()
            ->assertJsonPath('data.day_of_week', 'MWF')
            ->assertJsonPath('data.start_time', '09:00')
            ->assertJsonPath('data.end_time', '10:30');
    }

    public function test_course_offering_rejects_an_end_time_before_the_start_time(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $faculty = User::factory()->create(['role' => 'faculty']);
        $course = Course::factory()->create();
        $term = AcademicTerm::factory()->create();

        $this->actingAs($admin, 'api')->postJson('/api/v1/course-offerings', [
            'course_id' => $course->id,
            'academic_term_id' => $term->id,
            'instructor_id' => $faculty->id,
            'section' => '001',
            'day_of_week' => 'TTh',
            'start_time' => '14:30',
            'end_time' => '13:00',
            'delivery_mode' => 'in_person',
            'capacity' => 30,
            'status' => 'open',
        ])->assertUnprocessable()
            ->assertJsonValidationErrors(['end_time']);
    }
}
