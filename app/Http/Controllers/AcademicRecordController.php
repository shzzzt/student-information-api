<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ApiResponse;
use App\Models\Student;
use App\Services\AcademicRecordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class AcademicRecordController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly AcademicRecordService $records) {}

    public function show(Student $student): JsonResponse
    {
        if (! Gate::forUser(auth('api')->user())->allows('viewNestedRecords', $student)) {
            return $this->failure('You are not authorized to view this academic record.', 403);
        }

        return $this->success('Academic record retrieved successfully.', $this->records->forStudent($student));
    }
}
