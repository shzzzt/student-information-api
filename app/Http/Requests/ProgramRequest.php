<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class ProgramRequest extends ApiFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $required = $this->isMethod('POST') || $this->isMethod('PUT') ? 'required' : 'sometimes';
        $program = $this->route('program');
        $programId = is_object($program) ? $program->getKey() : $program;

        return [
            'code' => [$required, 'string', 'max:20', Rule::unique('programs', 'code')->ignore($programId)],
            'name' => [$required, 'string', 'max:150'],
            'department' => ['nullable', 'string', 'max:150'],
            'degree_level' => [$required, 'in:certificate,associate,bachelor,master,doctorate'],
            'total_credits' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'status' => [$required, 'in:active,inactive,archived'],
        ];
    }
}
