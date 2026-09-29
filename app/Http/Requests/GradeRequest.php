<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class GradeRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('POST') || $this->isMethod('PUT') ? 'required' : 'sometimes';
        $grade = $this->route('grade');
        $gradeId = is_object($grade) ? $grade->getKey() : $grade;

        return [
            'enrollment_id' => [$required, 'integer', 'exists:enrollments,id', Rule::unique('grades', 'enrollment_id')->ignore($gradeId)],
            'letter_grade' => ['nullable', 'string', 'max:5'],
            'numeric_grade' => ['nullable', 'numeric', 'between:0,100'],
            'grade_points' => ['nullable', 'numeric', 'between:0,4'],
            'status' => [$required, 'in:pending,submitted,approved,disputed,revised'],
            'graded_at' => ['nullable', 'date'],
            'remarks' => ['nullable', 'string'],
        ];
    }
}
