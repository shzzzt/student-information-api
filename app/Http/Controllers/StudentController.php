<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentIndexRequest;
use App\Http\Requests\StudentRequest;
use App\Http\Controllers\Concerns\ApiResponse;
use App\Models\Student;
use App\Services\StudentService;
use Illuminate\Http\JsonResponse;

class StudentController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly StudentService $students)
    {
    }

    public function index(StudentIndexRequest $request): JsonResponse
    {
        if ($this->isStudent()) {
            return $this->failure('Students cannot list all student records.', 403);
        }

        $paginator = $this->students->paginate($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Students retrieved successfully.',
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
            ],
        ]);
    }

    public function store(StudentRequest $request): JsonResponse
    {
        if ($this->isStudent()) {
            return $this->failure('Students cannot be created by student accounts.', 403);
        }

        return $this->ok('Student created successfully.', $this->students->create($request->validated()), 201);
    }

    public function show(Student $student): JsonResponse
    {
        if ($this->isStudent() && $student->user_id !== auth('api')->id()) {
            return $this->failure('You are not authorized to view this student.', 403);
        }

        return $this->ok('Student retrieved successfully.', $student->load('program'));
    }

    public function update(StudentRequest $request, Student $student): JsonResponse
    {
        if ($this->isStudent()) {
            return $this->failure('Students cannot update student records.', 403);
        }

        return $this->ok('Student updated successfully.', $this->students->update($student, $request->validated()));
    }

    public function destroy(Student $student): JsonResponse
    {
        if ($this->isStudent()) {
            return $this->failure('Students cannot deactivate student records.', 403);
        }

        return $this->ok('Student deactivated successfully.', $this->students->deactivate($student));
    }

    private function ok(string $message, mixed $data, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    private function isStudent(): bool
    {
        return auth('api')->user()?->role === 'student';
    }
}
