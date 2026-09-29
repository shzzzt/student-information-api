<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class EnrollmentRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('POST') || $this->isMethod('PUT') ? 'required' : 'sometimes';
        $enrollment = $this->route('enrollment');
        $enrollmentId = is_object($enrollment) ? $enrollment->getKey() : $enrollment;

        return [
            'student_id' => [$required, 'integer', 'exists:students,id'],
            'course_offering_id' => [$required, 'integer', 'exists:course_offerings,id'],
            'enrolled_on' => [$required, 'date'],
            'dropped_on' => ['nullable', 'date', 'after_or_equal:enrolled_on'],
            'status' => [$required, Rule::in(['enrolled', 'waitlisted', 'dropped', 'withdrawn', 'completed'])],
            'grade_mode' => [$required, Rule::in(['graded', 'pass_fail', 'audit'])],
        ];
    }
}
