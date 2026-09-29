<?php

namespace App\Services;

use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class StudentService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Student::query()->with('program');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($query) use ($search): void {
                $term = '%'.strtolower($search).'%';
                $query->whereRaw('LOWER(first_name) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(last_name) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(student_number) LIKE ?', [$term]);
            });
        }

        foreach (['program_id', 'year_level', 'status'] as $field) {
            if (array_key_exists($field, $filters) && $filters[$field] !== null) {
                $query->where($field, $filters[$field]);
            }
        }

        [$sort, $direction] = $this->sort($filters);

        return $query
            ->orderBy($sort, $direction)
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();
    }

    private function sort(array $filters): array
    {
        $sort = $filters['sort'] ?? 'last_name';
        $direction = $filters['direction'] ?? 'asc';

        if (str_starts_with($sort, '-')) {
            $sort = ltrim($sort, '-');
            $direction = 'desc';
        }

        return [$sort, $direction];
    }

    public function create(array $data): Student
    {
        $email = $data['email'] ?? null;
        unset($data['email']);

        $student = Student::create($data);

        if ($email !== null) {
            $student->user()->update(['email' => $email]);
        }

        return $student->load('program');
    }

    public function update(Student $student, array $data): Student
    {
        $email = $data['email'] ?? null;
        unset($data['email']);

        $student->update($data);

        if ($email !== null) {
            $student->user()->update(['email' => $email]);
        }

        return $student->refresh()->load('program');
    }

    public function deactivate(Student $student): Student
    {
        $student->update(['status' => 'inactive']);

        return $student->refresh()->load('program');
    }
}
