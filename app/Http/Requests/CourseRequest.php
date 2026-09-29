<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class CourseRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('POST') || $this->isMethod('PUT') ? 'required' : 'sometimes';
        $course = $this->route('course');
        $courseId = is_object($course) ? $course->getKey() : $course;

        return [
            'course_code' => [$required, 'string', 'max:20', Rule::unique('courses', 'course_code')->ignore($courseId)],
            'title' => [$required, 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'credits' => ['nullable', 'integer', 'min:0', 'max:255'],
            'lecture_hours' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'lab_hours' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'program_id' => ['nullable', 'integer', 'exists:programs,id'],
            'status' => [$required, 'in:active,inactive,archived'],
        ];
    }
}
