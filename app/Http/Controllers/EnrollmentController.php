<?php

namespace App\Http\Controllers;

use App\Exceptions\DuplicateEnrollmentException;
use App\Http\Controllers\Concerns\ApiResponse;
use App\Http\Requests\EnrollmentIndexRequest;
use App\Http\Requests\EnrollmentRequest;
use App\Models\CourseOffering;
use App\Models\Enrollment;
use App\Models\Student;
use App\Services\EnrollmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class EnrollmentController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly EnrollmentService $enrollments) {}

    public function index(EnrollmentIndexRequest $request): JsonResponse
    {
        return $this->success('Enrollments retrieved successfully.', $this->enrollments->paginate($request->validated()));
    }

    public function store(EnrollmentRequest $request): JsonResponse
    {
        try {
            return $this->success('Enrollment created successfully.', $this->enrollments->create($request->validated()), 201);
        } catch (DuplicateEnrollmentException $exception) {
            return $this->failure($exception->getMessage(), 409);
        }
    }

    public function show(Enrollment $enrollment): JsonResponse
    {
        if ($this->isStudent() && $enrollment->student_id !== auth('api')->user()->student?->id) {
            return $this->failure('You are not authorized to view this enrollment.', 403);
        }

        if ($this->isInstructor() && $enrollment->courseOffering()->where('instructor_id', auth('api')->id())->doesntExist()) {
            return $this->failure('You are not authorized to view this enrollment.', 403);
        }

        return $this->success('Enrollment retrieved successfully.', $enrollment->load(['student', 'courseOffering.course', 'courseOffering.academicTerm', 'grade']));
    }

    public function update(EnrollmentRequest $request, Enrollment $enrollment): JsonResponse
    {
        if (! $this->canManage()) {
            return $this->failure('You are not authorized to update enrollments.', 403);
        }

        return $this->success('Enrollment updated successfully.', $this->enrollments->update($enrollment, $request->validated()));
    }

    public function destroy(Enrollment $enrollment): JsonResponse
    {
        if (! $this->canManage()) {
            return $this->failure('You are not authorized to delete enrollments.', 403);
        }

        $this->enrollments->delete($enrollment);

        return $this->success('Enrollment deleted successfully.');
    }

    public function studentEnrollments(Student $student): JsonResponse
    {
        if (! Gate::forUser(auth('api')->user())->allows('viewNestedRecords', $student)) {
            return $this->failure('You are not authorized to view these enrollments.', 403);
        }

        return $this->success('Student enrollments retrieved successfully.', $this->enrollments->studentEnrollments($student));
    }

    public function offeringStudents(CourseOffering $courseOffering): JsonResponse
    {
        if ($this->isStudent()) {
            return $this->failure('Students cannot view offering rosters.', 403);
        }

        if ($this->isInstructor() && $courseOffering->instructor_id !== auth('api')->id()) {
            return $this->failure('You are not authorized to view this offering.', 403);
        }

        $students = $courseOffering->enrollments()
            ->with('student')
            ->orderBy('enrolled_on')
            ->get()
            ->map(fn (Enrollment $enrollment) => $enrollment->student)
            ->values();

        return $this->success('Course offering students retrieved successfully.', $students);
    }

    private function canManage(): bool
    {
        return in_array(auth('api')->user()?->role, ['admin', 'registrar'], true);
    }

    private function isStudent(): bool
    {
        return auth('api')->user()?->role === 'student';
    }

    private function isInstructor(): bool
    {
        return in_array(auth('api')->user()?->role, ['faculty', 'instructor'], true);
    }
}
