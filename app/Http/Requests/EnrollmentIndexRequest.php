<?php

namespace App\Http\Requests;

class EnrollmentIndexRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'student_id' => ['nullable', 'integer', 'exists:students,id'],
            'course_offering_id' => ['nullable', 'integer', 'exists:course_offerings,id'],
            'status' => ['nullable', 'in:enrolled,waitlisted,dropped,withdrawn,completed'],
            'sort' => ['nullable', 'in:enrolled_on,status'],
            'direction' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
