<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

/**
 * Response payload schemas for the API resources returned inside `data`.
 */
#[OA\Schema(
    schema: 'UserResource',
    description: 'User account object. Returned by `GET /api/v1/users` (subset) and embedded as the `instructor` or `graded_by` relation.',
    required: ['id', 'first_name', 'last_name', 'email', 'role', 'status'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 3),
        new OA\Property(property: 'first_name', type: 'string', example: 'Ana'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Reyes'),
        new OA\Property(property: 'email', type: 'string', format: 'email', example: 'ana.reyes@example.com'),
        new OA\Property(property: 'role', type: 'string', enum: ['admin', 'registrar', 'faculty', 'instructor', 'advisor', 'student'], example: 'faculty'),
        new OA\Property(property: 'status', type: 'string', enum: ['active', 'inactive', 'suspended'], example: 'active'),
        new OA\Property(property: 'email_verified_at', type: 'string', format: 'date-time', nullable: true, example: '2026-08-01T08:00:00.000000Z'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', nullable: true, example: '2026-08-01T08:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', nullable: true, example: '2026-08-01T08:00:00.000000Z'),
    ],
)]
#[OA\Schema(
    schema: 'ProgramResource',
    description: 'Program object returned in `data`.',
    required: ['id', 'code', 'name', 'degree_level', 'status'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'code', type: 'string', example: 'BSIT'),
        new OA\Property(property: 'name', type: 'string', example: 'Bachelor of Science in Information Technology'),
        new OA\Property(property: 'department', type: 'string', nullable: true, example: 'College of Computing Studies'),
        new OA\Property(property: 'degree_level', type: 'string', enum: ['certificate', 'associate', 'bachelor', 'master', 'doctorate'], example: 'bachelor'),
        new OA\Property(property: 'total_credits', type: 'integer', nullable: true, example: 140),
        new OA\Property(property: 'status', type: 'string', enum: ['active', 'inactive', 'archived'], example: 'active'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-08-01T08:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-08-01T08:00:00.000000Z'),
    ],
)]
#[OA\Schema(
    schema: 'CourseResource',
    description: 'Course object returned in `data`. The `program` relation is included by `GET /api/v1/courses/{course}`.',
    required: ['id', 'course_code', 'title', 'status'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'course_code', type: 'string', example: 'IT101'),
        new OA\Property(property: 'title', type: 'string', example: 'Programming Fundamentals'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Introduction to algorithms and problem solving.'),
        new OA\Property(property: 'credits', type: 'integer', example: 3),
        new OA\Property(property: 'lecture_hours', type: 'integer', example: 3),
        new OA\Property(property: 'lab_hours', type: 'integer', example: 2),
        new OA\Property(property: 'program_id', type: 'integer', nullable: true, example: 1),
        new OA\Property(property: 'status', type: 'string', enum: ['active', 'inactive', 'archived'], example: 'active'),
        new OA\Property(property: 'program', ref: '#/components/schemas/ProgramResource', description: 'Only present when the request eager loads the program.', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-08-01T08:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-08-01T08:00:00.000000Z'),
    ],
)]
#[OA\Schema(
    schema: 'AcademicTermResource',
    description: 'Academic term object returned in `data`.',
    required: ['id', 'code', 'name', 'term_type', 'academic_year', 'start_date', 'end_date', 'status'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'code', type: 'string', example: '2026-FALL'),
        new OA\Property(property: 'name', type: 'string', example: 'First Semester AY 2026-2027'),
        new OA\Property(property: 'term_type', type: 'string', enum: ['fall', 'spring', 'summer', 'winter', 'intersession'], example: 'fall'),
        new OA\Property(property: 'academic_year', type: 'integer', example: 2026),
        new OA\Property(property: 'start_date', type: 'string', format: 'date', example: '2026-08-01'),
        new OA\Property(property: 'end_date', type: 'string', format: 'date', example: '2026-12-15'),
        new OA\Property(property: 'registration_start', type: 'string', format: 'date', nullable: true, example: '2026-07-15'),
        new OA\Property(property: 'registration_end', type: 'string', format: 'date', nullable: true, example: '2026-07-31'),
        new OA\Property(property: 'status', type: 'string', enum: ['planned', 'open', 'closed', 'archived'], example: 'open'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-08-01T08:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-08-01T08:00:00.000000Z'),
    ],
)]
#[OA\Schema(
    schema: 'StudentResource',
    description: 'Student profile object returned in `data`. The `program` relation is present on every student endpoint response.',
    required: ['id', 'user_id', 'student_number', 'first_name', 'last_name', 'program_id', 'year_level', 'admission_date', 'status'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'user_id', type: 'integer', example: 12),
        new OA\Property(property: 'student_number', type: 'string', example: '2026-0101'),
        new OA\Property(property: 'first_name', type: 'string', example: 'Maria'),
        new OA\Property(property: 'middle_name', type: 'string', nullable: true, example: 'Lopez'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Santos'),
        new OA\Property(property: 'program_id', type: 'integer', example: 1),
        new OA\Property(property: 'year_level', type: 'integer', example: 2),
        new OA\Property(property: 'date_of_birth', type: 'string', format: 'date', nullable: true, example: '2006-03-14'),
        new OA\Property(property: 'gender', type: 'string', enum: ['male', 'female', 'other', 'prefer_not_to_say'], nullable: true, example: 'female'),
        new OA\Property(property: 'phone', type: 'string', nullable: true, example: '0917-000-0000'),
        new OA\Property(property: 'address', type: 'string', nullable: true, example: '123 Mabini Street, Manila'),
        new OA\Property(property: 'admission_date', type: 'string', format: 'date', example: '2026-08-01'),
        new OA\Property(property: 'expected_graduation_date', type: 'string', format: 'date', nullable: true, example: '2030-06-30'),
        new OA\Property(property: 'status', type: 'string', enum: ['active', 'inactive', 'on_leave', 'graduated', 'withdrawn', 'dismissed'], example: 'active'),
        new OA\Property(property: 'cumulative_gpa', type: 'string', nullable: true, description: 'Decimal cast, therefore serialized as a string.', example: '1.75'),
        new OA\Property(property: 'program', ref: '#/components/schemas/ProgramResource', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-08-01T08:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-08-01T08:00:00.000000Z'),
        new OA\Property(property: 'deleted_at', type: 'string', format: 'date-time', nullable: true, example: null),
    ],
)]
#[OA\Schema(
    schema: 'CourseOfferingResource',
    description: 'Course offering object returned in `data`. Relation keys are `course`, `academic_term` and `instructor`; the index endpoint returns the raw model with the same relation keys.',
    required: ['id', 'course_id', 'academic_term_id', 'instructor_id', 'section', 'day_of_week', 'start_time', 'end_time', 'delivery_mode', 'capacity', 'status'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 4),
        new OA\Property(property: 'course_id', type: 'integer', example: 1),
        new OA\Property(property: 'academic_term_id', type: 'integer', example: 1),
        new OA\Property(property: 'instructor_id', type: 'integer', example: 3),
        new OA\Property(property: 'section', type: 'string', example: '001'),
        new OA\Property(property: 'room', type: 'string', nullable: true, example: 'CCS 201'),
        new OA\Property(property: 'day_of_week', type: 'string', enum: ['MWF', 'TTh', 'Sat', 'Sun'], example: 'MWF'),
        new OA\Property(property: 'start_time', type: 'string', example: '09:00'),
        new OA\Property(property: 'end_time', type: 'string', example: '10:30'),
        new OA\Property(property: 'delivery_mode', type: 'string', enum: ['in_person', 'online', 'hybrid'], example: 'in_person'),
        new OA\Property(property: 'capacity', type: 'integer', example: 30),
        new OA\Property(property: 'enrolled_count', type: 'integer', example: 1),
        new OA\Property(property: 'status', type: 'string', enum: ['scheduled', 'open', 'closed', 'cancelled', 'completed'], example: 'open'),
        new OA\Property(property: 'course', ref: '#/components/schemas/CourseResource', nullable: true),
        new OA\Property(property: 'academic_term', ref: '#/components/schemas/AcademicTermResource', nullable: true),
        new OA\Property(property: 'instructor', ref: '#/components/schemas/UserResource', nullable: true),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-08-01T08:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-08-01T08:00:00.000000Z'),
    ],
)]
#[OA\Schema(
    schema: 'EnrollmentResource',
    description: 'Enrollment object returned in `data`. Relation keys are `student`, `course_offering` (with `course` and `academic_term`) and `grade`.',
    required: ['id', 'student_id', 'course_offering_id', 'enrolled_on', 'status', 'grade_mode'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'student_id', type: 'integer', example: 1),
        new OA\Property(property: 'course_offering_id', type: 'integer', example: 4),
        new OA\Property(property: 'enrolled_on', type: 'string', format: 'date', example: '2026-08-01'),
        new OA\Property(property: 'dropped_on', type: 'string', format: 'date', nullable: true, example: null),
        new OA\Property(property: 'status', type: 'string', enum: ['enrolled', 'waitlisted', 'dropped', 'withdrawn', 'completed'], example: 'enrolled'),
        new OA\Property(property: 'grade_mode', type: 'string', enum: ['graded', 'pass_fail', 'audit'], example: 'graded'),
        new OA\Property(property: 'student', ref: '#/components/schemas/StudentResource', nullable: true),
        new OA\Property(property: 'course_offering', ref: '#/components/schemas/CourseOfferingResource', nullable: true),
        new OA\Property(property: 'grade', ref: '#/components/schemas/GradeResource', nullable: true, description: '`null` until a grade is recorded.'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-08-01T08:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-08-01T08:00:00.000000Z'),
    ],
)]
#[OA\Schema(
    schema: 'GradeResource',
    description: 'Grade object returned in `data`. `graded_by` holds the grading user id until the `gradedBy` relation is loaded, in which case it contains the user object.',
    required: ['id', 'enrollment_id', 'status'],
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'enrollment_id', type: 'integer', example: 1),
        new OA\Property(property: 'letter_grade', type: 'string', nullable: true, example: 'A-'),
        new OA\Property(property: 'numeric_grade', type: 'string', nullable: true, description: 'Decimal cast, therefore serialized as a string.', example: '92.50'),
        new OA\Property(property: 'grade_points', type: 'string', nullable: true, description: 'Decimal cast, therefore serialized as a string.', example: '3.70'),
        new OA\Property(property: 'status', type: 'string', enum: ['pending', 'submitted', 'approved', 'disputed', 'revised'], example: 'submitted'),
        new OA\Property(property: 'graded_at', type: 'string', format: 'date-time', nullable: true, example: '2026-10-15T09:30:00.000000Z'),
        new OA\Property(property: 'remarks', type: 'string', nullable: true, example: 'Good standing.'),
        new OA\Property(property: 'enrollment', ref: '#/components/schemas/EnrollmentResource', nullable: true),
        new OA\Property(property: 'graded_by', ref: '#/components/schemas/UserResource', nullable: true, description: 'User id, or the loaded grading user object.'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-10-15T09:30:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-10-15T09:30:00.000000Z'),
    ],
)]
#[OA\Schema(
    schema: 'AcademicRecordResource',
    description: 'Academic record returned by `GET /api/v1/students/{student}/academic-record`: a student summary plus enrollments grouped by academic term.',
    required: ['student', 'terms'],
    properties: [
        new OA\Property(property: 'student', description: 'Student summary (subset of the student fields).', properties: [
            new OA\Property(property: 'id', type: 'integer', example: 1),
            new OA\Property(property: 'student_number', type: 'string', example: '2026-0101'),
            new OA\Property(property: 'first_name', type: 'string', example: 'Maria'),
            new OA\Property(property: 'middle_name', type: 'string', nullable: true, example: 'Lopez'),
            new OA\Property(property: 'last_name', type: 'string', example: 'Santos'),
            new OA\Property(property: 'program_id', type: 'integer', example: 1),
            new OA\Property(property: 'year_level', type: 'integer', example: 2),
            new OA\Property(property: 'status', type: 'string', example: 'active'),
        ], type: 'object'),
        new OA\Property(property: 'terms', description: 'One entry per academic term that has enrollments.', type: 'array', items: new OA\Items(properties: [
            new OA\Property(property: 'academic_term', ref: '#/components/schemas/AcademicTermResource'),
            new OA\Property(property: 'enrollments', type: 'array', items: new OA\Items(ref: '#/components/schemas/EnrollmentResource')),
        ], type: 'object'), example: [[
            'academic_term' => ['id' => 1, 'code' => '2026-FALL', 'name' => 'First Semester AY 2026-2027', 'term_type' => 'fall', 'academic_year' => 2026, 'status' => 'open'],
            'enrollments' => [['id' => 1, 'student_id' => 1, 'course_offering_id' => 4, 'enrolled_on' => '2026-08-01', 'status' => 'enrolled', 'grade_mode' => 'graded', 'grade' => ['id' => 1, 'enrollment_id' => 1, 'numeric_grade' => '92.50', 'status' => 'submitted']]],
        ]]),
    ],
)]
class ResourceSchemas {}
