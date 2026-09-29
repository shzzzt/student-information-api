<?php

namespace App\Http\Controllers;

use App\Http\Requests\CourseIndexRequest;
use App\Http\Requests\CourseRequest;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    public function __construct(private readonly CourseService $courses)
    {
    }

    public function index(CourseIndexRequest $request): JsonResponse
    {
        return $this->ok('Courses retrieved successfully.', $this->courses->paginate($request->validated()));
    }

    public function store(CourseRequest $request): JsonResponse
    {
        return $this->ok('Course created successfully.', $this->courses->create($request->validated()), 201);
    }

    public function show(Course $course): JsonResponse
    {
        return $this->ok('Course retrieved successfully.', $course->load('program'));
    }

    public function update(CourseRequest $request, Course $course): JsonResponse
    {
        return $this->ok('Course updated successfully.', $this->courses->update($course, $request->validated()));
    }

    public function destroy(Course $course): JsonResponse
    {
        $this->courses->delete($course);

        return $this->ok('Course deleted successfully.', null);
    }

    private function ok(string $message, mixed $data, int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}
