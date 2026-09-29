<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class StudentRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('POST') || $this->isMethod('PUT') ? 'required' : 'sometimes';
        $student = $this->route('student');
        $studentId = is_object($student) ? $student->getKey() : $student;
        $userId = is_object($student) ? $student->user_id : null;

        return [
            'user_id' => [$required, 'integer', 'exists:users,id', Rule::unique('students', 'user_id')->ignore($studentId)],
            'student_number' => [$required, 'string', 'max:30', Rule::unique('students', 'student_number')->ignore($studentId)],
            'first_name' => [$required, 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => [$required, 'string', 'max:100'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->input('user_id', $userId))],
            'program_id' => [$required, 'integer', 'exists:programs,id'],
            'year_level' => [$required, 'integer', 'between:1,5'],
            'date_of_birth' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other', 'prefer_not_to_say'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'admission_date' => [$required, 'date'],
            'expected_graduation_date' => ['nullable', 'date', 'after_or_equal:admission_date'],
            'status' => [$required, Rule::in(['active', 'inactive', 'on_leave', 'graduated', 'withdrawn', 'dismissed'])],
            'cumulative_gpa' => ['nullable', 'numeric', 'between:0,4.00'],
        ];
    }
}
