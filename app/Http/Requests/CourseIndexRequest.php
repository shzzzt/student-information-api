<?php

namespace App\Http\Requests;

class CourseIndexRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:200'],
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
            'status' => ['nullable', 'in:active,inactive,archived'],
            'sort' => ['nullable', 'in:course_code,title'],
            'direction' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
