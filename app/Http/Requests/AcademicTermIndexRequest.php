<?php

namespace App\Http\Requests;

class AcademicTermIndexRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'term_type' => ['nullable', 'in:fall,spring,summer,winter,intersession'],
            'academic_year' => ['nullable', 'integer', 'between:1900,2200'],
            'status' => ['nullable', 'in:planned,open,closed,archived'],
            'sort' => ['nullable', 'in:code,academic_year,start_date'],
            'direction' => ['nullable', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
