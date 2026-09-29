<?php

namespace App\Http\Requests;

class CourseOfferingIndexRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:200'],
            'course_id' => ['nullable', 'integer', 'exists:courses,id'],
            'academic_term_id' => ['nullable', 'integer', 'exists:academic_terms,id'],
            'instructor_id' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['nullable', 'in:scheduled,open,closed,cancelled,completed'],
            'sort' => ['nullable', 'in:section,capacity,status'],
            'direction' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
