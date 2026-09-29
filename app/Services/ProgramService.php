<?php

namespace App\Services;

use App\Models\Program;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProgramService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Program::query();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($query) use ($search): void {
                $term = '%'.strtolower($search).'%';
                $query->whereRaw('LOWER(code) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(name) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(department) LIKE ?', [$term]);
            });
        }

        foreach (['degree_level', 'status'] as $field) {
            if (array_key_exists($field, $filters) && $filters[$field] !== null) {
                $query->where($field, $filters[$field]);
            }
        }

        return $query
            ->orderBy($filters['sort'] ?? 'name', $filters['direction'] ?? 'asc')
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();
    }

    public function create(array $data): Program
    {
        return Program::create($data);
    }

    public function update(Program $program, array $data): Program
    {
        $program->update($data);

        return $program->refresh();
    }

    public function delete(Program $program): void
    {
        $program->delete();
    }
}
