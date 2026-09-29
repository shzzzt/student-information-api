<?php

namespace App\Http\Controllers;

use App\Http\Requests\AcademicTermIndexRequest;
use App\Http\Requests\AcademicTermRequest;
use App\Models\AcademicTerm;
use App\Services\AcademicTermService;
use Illuminate\Http\JsonResponse;

class AcademicTermController extends Controller
{
    public function __construct(private readonly AcademicTermService $terms)
    {
    }

    public function index(AcademicTermIndexRequest $request): JsonResponse
    {
        return $this->ok('Academic terms retrieved successfully.', $this->terms->paginate($request->validated()));
    }

    public function store(AcademicTermRequest $request): JsonResponse
    {
        return $this->ok('Academic term created successfully.', $this->terms->create($request->validated()), 201);
    }

    public function show(AcademicTerm $academicTerm): JsonResponse
    {
        return $this->ok('Academic term retrieved successfully.', $academicTerm);
    }

    public function update(AcademicTermRequest $request, AcademicTerm $academicTerm): JsonResponse
    {
        return $this->ok('Academic term updated successfully.', $this->terms->update($academicTerm, $request->validated()));
    }

    public function destroy(AcademicTerm $academicTerm): JsonResponse
    {
        $this->terms->delete($academicTerm);

        return $this->ok('Academic term deleted successfully.', null);
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
