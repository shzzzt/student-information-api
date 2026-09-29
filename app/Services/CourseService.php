<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CourseService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Course::query()->with('program');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($query) use ($search): void {
                $term = '%'.strtolower($search).'%';
                $query->whereRaw('LOWER(course_code) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(title) LIKE ?', [$term]);
            });
        }

        foreach (['program_id', 'status'] as $field) {
            if (array_key_exists($field, $filters) && $filters[$field] !== null) {
                $query->where($field, $filters[$field]);
            }
        }

        return $query
            ->orderBy($filters['sort'] ?? 'course_code', $filters['direction'] ?? 'asc')
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();
    }

    public function create(array $data): Course
    {
        return Course::create($data)->load('program');
    }

    public function update(Course $course, array $data): Course
    {
        $course->update($data);

        return $course->refresh()->load('program');
    }

    public function delete(Course $course): void
    {
        $course->delete();
    }
}
