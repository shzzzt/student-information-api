<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class CourseOfferingRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('POST') || $this->isMethod('PUT') ? 'required' : 'sometimes';
        $offering = $this->route('course_offering');
        $offeringId = is_object($offering) ? $offering->getKey() : $offering;

        return [
            'course_id' => [$required, 'integer', 'exists:courses,id'],
            'academic_term_id' => [$required, 'integer', 'exists:academic_terms,id'],
            'instructor_id' => [$required, 'integer', Rule::exists('users', 'id')->where(fn ($query) => $query->whereIn('role', ['faculty', 'instructor']))],
            'section' => [$required, 'string', 'max:10'],
            'room' => ['nullable', 'string', 'max:50'],
            'day_of_week' => [$required, 'string', Rule::in(['MWF', 'TTh', 'Sat', 'Sun'])],
            'start_time' => [$required, 'date_format:H:i'],
            'end_time' => [$required, 'date_format:H:i', 'after:start_time'],
            'delivery_mode' => [$required, 'in:in_person,online,hybrid'],
            'capacity' => [$required, 'integer', 'min:1', 'max:65535'],
            'enrolled_count' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'status' => [$required, 'in:scheduled,open,closed,cancelled,completed'],
        ];
    }
}
