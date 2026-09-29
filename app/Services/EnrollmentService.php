<?php

namespace App\Services;

use App\Exceptions\DuplicateEnrollmentException;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class EnrollmentService
{
    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Enrollment::query()->with(['student', 'courseOffering.course', 'courseOffering.academicTerm', 'grade']);

        foreach (['student_id', 'course_offering_id', 'status'] as $field) {
            if (array_key_exists($field, $filters) && $filters[$field] !== null) {
                $query->where($field, $filters[$field]);
            }
        }

        if (auth('api')->user()?->role === 'student') {
            $query->where('student_id', auth('api')->user()->student?->id ?? 0);
        }

        if (in_array(auth('api')->user()?->role, ['faculty', 'instructor'], true)) {
            $query->whereHas('courseOffering', fn ($offering) => $offering->where('instructor_id', auth('api')->id()));
        }

        return $query
            ->orderBy($filters['sort'] ?? 'enrolled_on', $filters['direction'] ?? 'desc')
            ->paginate($filters['per_page'] ?? 20)
            ->withQueryString();
    }

    public function create(array $data): Enrollment
    {
        try {
            return DB::transaction(function () use ($data): Enrollment {
                if (Enrollment::where('student_id', $data['student_id'])
                    ->where('course_offering_id', $data['course_offering_id'])
                    ->exists()) {
                    throw new DuplicateEnrollmentException('The student is already enrolled in this course offering.');
                }

                $enrollment = Enrollment::create($data);

                if ($enrollment->status === 'enrolled') {
                    $enrollment->courseOffering()->increment('enrolled_count');
                }

                return $enrollment->load(['student', 'courseOffering.course', 'courseOffering.academicTerm', 'grade']);
            });
        } catch (QueryException $exception) {
            if (in_array($exception->getCode(), ['23000', '23505'], true)) {
                throw new DuplicateEnrollmentException('The student is already enrolled in this course offering.', 0, $exception);
            }

            throw $exception;
        }
    }

    public function update(Enrollment $enrollment, array $data): Enrollment
    {
        return DB::transaction(function () use ($enrollment, $data): Enrollment {
            $wasEnrolled = $enrollment->status === 'enrolled';
            $enrollment->update($data);
            $isEnrolled = $enrollment->status === 'enrolled';

            if ($wasEnrolled !== $isEnrolled) {
                $enrollment->courseOffering()->increment('enrolled_count', $isEnrolled ? 1 : -1);
            }

            return $enrollment->refresh()->load(['student', 'courseOffering.course', 'courseOffering.academicTerm', 'grade']);
        });
    }

    public function delete(Enrollment $enrollment): void
    {
        DB::transaction(function () use ($enrollment): void {
            if ($enrollment->status === 'enrolled') {
                $enrollment->courseOffering()->decrement('enrolled_count');
            }

            $enrollment->delete();
        });
    }

    public function studentEnrollments(Student $student): array
    {
        return $student->enrollments()
            ->with(['courseOffering.course', 'courseOffering.academicTerm', 'grade'])
            ->orderByDesc('enrolled_on')
            ->get()
            ->all();
    }
}
