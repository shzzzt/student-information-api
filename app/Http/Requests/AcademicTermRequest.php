<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class AcademicTermRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('POST') || $this->isMethod('PUT') ? 'required' : 'sometimes';
        $term = $this->route('academic_term');
        $termId = is_object($term) ? $term->getKey() : $term;

        return [
            'code' => [$required, 'string', 'max:20', Rule::unique('academic_terms', 'code')->ignore($termId)],
            'name' => [$required, 'string', 'max:100'],
            'term_type' => [$required, 'in:fall,spring,summer,winter,intersession'],
            'academic_year' => [$required, 'integer', 'between:1900,2200'],
            'start_date' => [$required, 'date'],
            'end_date' => [$required, 'date', 'after_or_equal:start_date'],
            'registration_start' => ['nullable', 'date'],
            'registration_end' => ['nullable', 'date', 'after_or_equal:registration_start'],
            'status' => [$required, 'in:planned,open,closed,archived'],
        ];
    }
}
