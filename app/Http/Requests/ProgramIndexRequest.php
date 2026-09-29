<?php

namespace App\Http\Requests;

class ProgramIndexRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:150'],
            'degree_level' => ['nullable', 'in:certificate,associate,bachelor,master,doctorate'],
            'status' => ['nullable', 'in:active,inactive,archived'],
            'sort' => ['nullable', 'in:code,name'],
            'direction' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
