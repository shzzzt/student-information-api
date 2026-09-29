<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ApiResponse;
use App\Http\Requests\GradeIndexRequest;
use App\Http\Requests\GradeRequest;
use App\Models\Grade;
use App\Models\Student;
use App\Services\GradeService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class GradeController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly GradeService $grades) {}

    public function index(GradeIndexRequest $request): JsonResponse
    {
        return $this->success('Grades retrieved successfully.', $this->grades->paginate($request->validated()));
    }

    public function store(GradeRequest $request): JsonResponse
    {
        try {
            return $this->success('Grade created successfully.', $this->grades->create($request->validated()), 201);
        } catch (AuthorizationException $exception) {
            return $this->failure($exception->getMessage(), 403);
        }
    }

    public function show(Grade $grade): JsonResponse
    {
        if ($this->isStudent() && $grade->enrollment()->where('student_id', auth('api')->user()->student?->id)->doesntExist()) {
            return $this->failure('You are not authorized to view this grade.', 403);
        }

        if ($this->isInstructor() && $grade->enrollment->courseOffering->instructor_id !== auth('api')->id()) {
            return $this->failure('You are not authorized to view this grade.', 403);
        }

        return $this->success('Grade retrieved successfully.', $grade->load(['enrollment.student', 'enrollment.courseOffering.course', 'enrollment.courseOffering.academicTerm', 'gradedBy']));
    }

    public function update(GradeRequest $request, Grade $grade): JsonResponse
    {
        try {
            return $this->success('Grade updated successfully.', $this->grades->update($grade, $request->validated()));
        } catch (AuthorizationException $exception) {
            return $this->failure($exception->getMessage(), 403);
        }
    }

    public function studentGrades(Student $student): JsonResponse
    {
        if (! Gate::forUser(auth('api')->user())->allows('viewNestedRecords', $student)) {
            return $this->failure('You are not authorized to view these grades.', 403);
        }

        return $this->success('Student grades retrieved successfully.', $this->grades->studentGrades($student->id));
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
