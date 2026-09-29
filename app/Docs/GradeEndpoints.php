<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

/**
 * OpenAPI annotations for the `/api/v1/grades` resource and the nested student grades endpoint.
 */
class GradeEndpoints
{
    #[OA\Get(
        path: '/api/v1/grades',
        operationId: 'listGrades',
        summary: 'List grades',
        description: 'Purpose: browse grades; students only receive their own grades and faculty or instructors only grades of their own offerings. '
            .'Auth: Bearer JWT. Roles: admin, registrar, faculty, instructor, advisor, student.',
        tags: ['Grades'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'student_id', in: 'query', description: 'Filter by student id (`students.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
            new OA\Parameter(name: 'enrollment_id', in: 'query', description: 'Filter by enrollment id (`enrollments.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string', enum: ['pending', 'submitted', 'approved', 'disputed', 'revised']), example: 'submitted'),
            new OA\Parameter(name: 'sort', in: 'query', schema: new OA\Schema(type: 'string', enum: ['graded_at', 'status', 'numeric_grade']), example: 'graded_at'),
            new OA\Parameter(name: 'direction', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc']), example: 'desc'),
            new OA\Parameter(name: 'per_page', in: 'query', description: 'Page size (1-100), default 20.', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100), example: 20),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Grades retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/GradeCollectionResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - the authenticated role may not list grades.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - invalid query parameters.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function index(): void {}

    #[OA\Post(
        path: '/api/v1/grades',
        operationId: 'createGrade',
        summary: 'Create a grade',
        description: 'Purpose: record the grade of an enrollment. Only the instructor assigned to the enrollment offering may create it; `graded_by` is set from the token and `graded_at` defaults to now. '
            .'Auth: Bearer JWT. Roles: admin, faculty, instructor.',
        tags: ['Grades'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Grade payload. `enrollment_id` and `status` are required.',
            content: new OA\JsonContent(ref: '#/components/schemas/GradeInput')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Grade created successfully.', content: new OA\JsonContent(ref: '#/components/schemas/GradeItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - "Only the instructor assigned to this offering may manage its grades." or the role is not allowed.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - the enrollment already has a grade.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - unknown enrollment or a grade outside the 0-100 / 0-4 range.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function store(): void {}
    #[OA\Get(
        path: '/api/v1/grades/{grade}',
        operationId: 'getGrade',
        summary: 'Get one grade',
        description: 'Purpose: read a single grade with its enrollment, student, course offering and grading user. Auth: Bearer JWT. '
            .'Roles: admin, registrar, faculty, instructor, advisor; students may only read their own grades and instructors only grades of their own offerings.',
        tags: ['Grades'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'grade', in: 'path', required: true, description: 'Grade id (`grades.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Grade retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/GradeItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - the grade belongs to another student or to an offering of another instructor.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no grade matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function show(): void {}

    #[OA\Put(
        path: '/api/v1/grades/{grade}',
        operationId: 'replaceGrade',
        summary: 'Replace a grade',
        description: 'Purpose: replace a grade; every required field must be supplied. Auth: Bearer JWT. Roles: admin, faculty, instructor. '
            .'Only the instructor assigned to the offering may update its grades.',
        tags: ['Grades'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'grade', in: 'path', required: true, description: 'Grade id (`grades.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/GradeInput')),
        responses: [
            new OA\Response(response: 200, description: 'Grade updated successfully.', content: new OA\JsonContent(ref: '#/components/schemas/GradeItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - "Only the instructor assigned to this offering may manage its grades." or the role is not allowed.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no grade matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - the target enrollment already has a grade.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - unknown enrollment or a grade outside the 0-100 / 0-4 range.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function replace(): void {}
    #[OA\Patch(
        path: '/api/v1/grades/{grade}',
        operationId: 'updateGrade',
        summary: 'Update a grade',
        description: 'Purpose: partially update a grade, for example revising the numeric grade. Auth: Bearer JWT. Roles: admin, faculty, instructor. '
            .'Only the instructor assigned to the offering may update its grades.',
        tags: ['Grades'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'grade', in: 'path', required: true, description: 'Grade id (`grades.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Any subset of the grade fields, for example `{"numeric_grade": 90, "status": "revised"}`.',
            content: new OA\JsonContent(ref: '#/components/schemas/GradeInput')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Grade updated successfully.', content: new OA\JsonContent(ref: '#/components/schemas/GradeItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - "Only the instructor assigned to this offering may manage its grades." or the role is not allowed.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no grade matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - invalid value for a supplied field.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function update(): void {}

    #[OA\Get(
        path: '/api/v1/students/{student}/grades',
        operationId: 'getStudentGrades',
        summary: 'List the grades of one student',
        description: 'Purpose: read every grade of one student as a non-paginated list, with the enrollment, course offering and grading user loaded. Auth: Bearer JWT. '
            .'Access is granted to admin and registrar, to the student that owns the record, and to faculty or advisors of an offering the student is enrolled in.',
        tags: ['Grades'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'student', in: 'path', required: true, description: 'Student id (`students.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Student grades retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/StudentGradeListResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - "You are not authorized to view these grades." (URL tampering by a student or an unassigned instructor).', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no student matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function studentGrades(): void {}
}
