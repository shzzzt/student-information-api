<?php

namespace App\Services;

use App\Models\AcademicTerm;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AcademicTermService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = AcademicTerm::query();

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($query) use ($search): void {
                $term = '%'.strtolower($search).'%';
                $query->whereRaw('LOWER(code) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(name) LIKE ?', [$term]);
            });
        }

        foreach (['term_type', 'academic_year', 'status'] as $field) {
            if (array_key_exists($field, $filters) && $filters[$field] !== null) {
                $query->where($field, $filters[$field]);
            }
        }

        return $query
            ->orderBy($filters['sort'] ?? 'academic_year', $filters['direction'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();
    }

    public function create(array $data): AcademicTerm
    {
        return AcademicTerm::create($data);
    }

    public function update(AcademicTerm $term, array $data): AcademicTerm
    {
        $term->update($data);

        return $term->refresh();
    }

    public function delete(AcademicTerm $term): void
    {
        $term->delete();
    }
}
