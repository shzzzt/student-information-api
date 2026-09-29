<?php

namespace App\Http\Requests;

class StudentIndexRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
            'year_level' => ['nullable', 'integer', 'between:1,5'],
            'status' => ['nullable', 'in:active,inactive,on_leave,graduated,withdrawn,dismissed'],
            'sort' => ['nullable', 'in:last_name,student_number,-last_name,-student_number'],
            'direction' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
