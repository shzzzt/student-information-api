<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Models\Grade;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;

class GradeService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Grade::query()->with(['enrollment.student', 'enrollment.courseOffering.course', 'enrollment.courseOffering.academicTerm', 'gradedBy']);

        if (isset($filters['student_id'])) {
            $query->whereHas('enrollment', fn ($enrollment) => $enrollment->where('student_id', $filters['student_id']));
        }

        if (in_array(auth('api')->user()?->role, ['faculty', 'instructor'], true)) {
            $query->whereHas('enrollment.courseOffering', fn ($offering) => $offering->where('instructor_id', auth('api')->id()));
        }

        if (auth('api')->user()?->role === 'student') {
            $query->whereHas('enrollment', fn ($enrollment) => $enrollment->where('student_id', auth('api')->user()->student?->id ?? 0));
        }

        foreach (['enrollment_id', 'status'] as $field) {
            if (array_key_exists($field, $filters) && $filters[$field] !== null) {
                $query->where($field, $filters[$field]);
            }
        }

        return $query
            ->orderBy($filters['sort'] ?? 'graded_at', $filters['direction'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();
    }

    public function create(array $data): Grade
    {
        $enrollment = Enrollment::with('courseOffering')->findOrFail($data['enrollment_id']);
        $this->assertAssignedInstructor($enrollment);

        $data['graded_by'] = auth('api')->id();
        $data['graded_at'] ??= now();

        return Grade::create($data)->load(['enrollment.student', 'enrollment.courseOffering.course', 'enrollment.courseOffering.academicTerm', 'gradedBy']);
    }

    public function update(Grade $grade, array $data): Grade
    {
        $this->assertAssignedInstructor($grade->enrollment()->with('courseOffering')->firstOrFail());

        if (isset($data['enrollment_id']) && $data['enrollment_id'] !== $grade->enrollment_id) {
            $newEnrollment = Enrollment::with('courseOffering')->findOrFail($data['enrollment_id']);
            $this->assertAssignedInstructor($newEnrollment);
        }

        $grade->update($data);

        return $grade->refresh()->load(['enrollment.student', 'enrollment.courseOffering.course', 'enrollment.courseOffering.academicTerm', 'gradedBy']);
    }

    public function studentGrades(int $studentId): array
    {
        return Grade::query()
            ->with(['enrollment.student', 'enrollment.courseOffering.course', 'enrollment.courseOffering.academicTerm', 'gradedBy'])
            ->whereHas('enrollment', fn ($query) => $query->where('student_id', $studentId))
            ->get()
            ->all();
    }

    private function assertAssignedInstructor(Enrollment $enrollment): void
    {
        if (in_array(auth('api')->user()?->role, ['admin', 'registrar'], true)) {
            return;
        }

        if (! in_array(auth('api')->user()?->role, ['faculty', 'instructor'], true)
            || (int) $enrollment->courseOffering->instructor_id !== (int) auth('api')->id()) {
            throw new AuthorizationException('Only the instructor assigned to this offering may manage its grades.');
        }
    }
}
