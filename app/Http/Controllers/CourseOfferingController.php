<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ApiResponse;
use App\Http\Requests\CourseOfferingIndexRequest;
use App\Http\Requests\CourseOfferingRequest;
use App\Http\Resources\CourseOfferingResource;
use App\Models\CourseOffering;
use App\Services\CourseOfferingService;
use Illuminate\Http\JsonResponse;

class CourseOfferingController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly CourseOfferingService $offerings) {}

    public function index(CourseOfferingIndexRequest $request): JsonResponse
    {
        if (auth('api')->user()?->role === 'student') {
            return $this->failure('Students cannot browse course offerings.', 403);
        }

        return $this->success('Course offerings retrieved successfully.', $this->offerings->paginate($request->validated()));
    }

    public function store(CourseOfferingRequest $request): JsonResponse
    {
        return $this->success('Course offering created successfully.', new CourseOfferingResource($this->offerings->create($request->validated())), 201);
    }

    public function show(CourseOffering $courseOffering): JsonResponse
    {
        if (auth('api')->user()?->role === 'student') {
            return $this->failure('Students cannot view course offerings.', 403);
        }

        if ($this->isInstructor() && $courseOffering->instructor_id !== auth('api')->id()) {
            return $this->failure('You are not authorized to view this offering.', 403);
        }

        return $this->success('Course offering retrieved successfully.', new CourseOfferingResource($courseOffering->load(['course', 'academicTerm', 'instructor'])));
    }

    public function update(CourseOfferingRequest $request, CourseOffering $courseOffering): JsonResponse
    {
        return $this->success('Course offering updated successfully.', new CourseOfferingResource($this->offerings->update($courseOffering, $request->validated())));
    }

    public function destroy(CourseOffering $courseOffering): JsonResponse
    {
        $this->offerings->delete($courseOffering);

        return $this->success('Course offering deleted successfully.');
    }

    private function isInstructor(): bool
    {
        return in_array(auth('api')->user()?->role, ['faculty', 'instructor'], true);
    }
}
