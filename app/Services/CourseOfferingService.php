<?php

namespace App\Services;

use App\Models\CourseOffering;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CourseOfferingService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = CourseOffering::query()->with(['course', 'academicTerm', 'instructor']);

        if (! empty($filters['search'])) {
            $term = '%'.strtolower($filters['search']).'%';
            $query->whereHas('course', function ($course) use ($term): void {
                $course->whereRaw('LOWER(course_code) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(title) LIKE ?', [$term]);
            });
        }

        foreach (['course_id', 'academic_term_id', 'instructor_id', 'status'] as $field) {
            if (array_key_exists($field, $filters) && $filters[$field] !== null) {
                $query->where($field, $filters[$field]);
            }
        }

        if (auth('api')->user()?->role === 'faculty' || auth('api')->user()?->role === 'instructor') {
            $query->where('instructor_id', auth('api')->id());
        }

        return $query
            ->orderBy($filters['sort'] ?? 'section', $filters['direction'] ?? 'asc')
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();
    }

    public function create(array $data): CourseOffering
    {
        return CourseOffering::create($data)->load(['course', 'academicTerm', 'instructor']);
    }

    public function update(CourseOffering $offering, array $data): CourseOffering
    {
        $offering->update($data);

        return $offering->refresh()->load(['course', 'academicTerm', 'instructor']);
    }

    public function delete(CourseOffering $offering): void
    {
        $offering->delete();
    }
}
