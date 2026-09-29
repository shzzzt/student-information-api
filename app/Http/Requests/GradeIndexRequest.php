<?php

namespace App\Http\Requests;

class GradeIndexRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'enrollment_id' => ['nullable', 'integer', 'exists:enrollments,id'],
            'status' => ['nullable', 'in:pending,submitted,approved,disputed,revised'],
            'sort' => ['nullable', 'in:graded_at,status,numeric_grade'],
            'direction' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
