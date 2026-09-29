<?php

namespace App\Services;

use App\Models\Student;

class AcademicRecordService
{
    public function forStudent(Student $student): array
    {
        $enrollments = $student->enrollments()
            ->with(['courseOffering.course', 'courseOffering.academicTerm', 'grade'])
            ->orderBy('enrolled_on')
            ->get();

        return [
            'student' => $student->only(['id', 'student_number', 'first_name', 'middle_name', 'last_name', 'program_id', 'year_level', 'status']),
            'terms' => $enrollments
                ->groupBy(fn ($enrollment) => $enrollment->courseOffering->academic_term_id)
                ->map(function ($termEnrollments): array {
                    $term = $termEnrollments->first()->courseOffering->academicTerm;

                    return [
                        'academic_term' => $term,
                        'enrollments' => $termEnrollments->values(),
                    ];
                })
                ->values()
                ->all(),
        ];
    }
}
