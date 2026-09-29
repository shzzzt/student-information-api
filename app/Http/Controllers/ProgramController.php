<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgramIndexRequest;
use App\Http\Requests\ProgramRequest;
use App\Models\Program;
use App\Services\ProgramService;
use Illuminate\Http\JsonResponse;

class ProgramController extends Controller
{
    public function __construct(private readonly ProgramService $programs)
    {
    }

    public function index(ProgramIndexRequest $request): JsonResponse
    {
        return $this->ok('Programs retrieved successfully.', $this->programs->paginate($request->validated()));
    }

    public function store(ProgramRequest $request): JsonResponse
    {
        return $this->ok('Program created successfully.', $this->programs->create($request->validated()), 201);
    }

    public function show(Program $program): JsonResponse
    {
        return $this->ok('Program retrieved successfully.', $program);
    }

    public function update(ProgramRequest $request, Program $program): JsonResponse
    {
        return $this->ok('Program updated successfully.', $this->programs->update($program, $request->validated()));
    }

    public function destroy(Program $program): JsonResponse
    {
        $this->programs->delete($program);

        return $this->ok('Program deleted successfully.', null);
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
