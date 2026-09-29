<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

/**
 * OpenAPI annotations for the `/api/v1/students` resource.
 */
class StudentEndpoints
{
    #[OA\Get(
        path: '/api/v1/students',
        operationId: 'listStudents',
        summary: 'List students',
        description: 'Purpose: browse student profiles with search, filters, sorting and pagination. '
            .'Auth: Bearer JWT. Roles: admin, registrar (a student account receives 403).',
        tags: ['Students'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', description: 'Case-insensitive match on first name, last name or student number.', schema: new OA\Schema(type: 'string', maxLength: 100), example: 'dela'),
            new OA\Parameter(name: 'program_id', in: 'query', description: 'Filter by program id (`programs.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
            new OA\Parameter(name: 'year_level', in: 'query', description: 'Filter by year level (1-5).', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 5), example: 3),
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string', enum: ['active', 'inactive', 'on_leave', 'graduated', 'withdrawn', 'dismissed']), example: 'active'),
            new OA\Parameter(name: 'sort', in: 'query', description: 'Sort field. Prefix with `-` for descending order.', schema: new OA\Schema(type: 'string', enum: ['last_name', 'student_number', '-last_name', '-student_number']), example: '-last_name'),
            new OA\Parameter(name: 'direction', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc']), example: 'asc'),
            new OA\Parameter(name: 'per_page', in: 'query', description: 'Page size (1-100).', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100), example: 20),
            new OA\Parameter(name: 'page', in: 'query', description: 'Page number.', schema: new OA\Schema(type: 'integer', minimum: 1), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Students retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/StudentIndexResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - the authenticated role may not list student records.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - invalid query parameters.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function index(): void {}

    #[OA\Post(
        path: '/api/v1/students',
        operationId: 'createStudent',
        summary: 'Create a student',
        description: 'Purpose: create a student profile linked to an existing user account. '
            .'Auth: Bearer JWT. Roles: admin, registrar (student accounts receive 403).',
        tags: ['Students'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Student payload. `user_id`, `student_number`, `first_name`, `last_name`, `program_id`, `year_level`, `admission_date` and `status` are required.',
            content: new OA\JsonContent(ref: '#/components/schemas/StudentInput')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Student created successfully.', content: new OA\JsonContent(ref: '#/components/schemas/StudentItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - the authenticated role may not create students.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - the user account or student number already has a student profile.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - unique student number, invalid email or unknown foreign keys.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function store(): void {}

    #[OA\Get(
        path: '/api/v1/students/{student}',
        operationId: 'getStudent',
        summary: 'Get one student',
        description: 'Purpose: read a single student profile with its program. '
            .'Auth: Bearer JWT. Roles: admin, registrar, and students for their own record only.',
        tags: ['Students'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'student', in: 'path', required: true, description: 'Student id (`students.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Student retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/StudentItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - a student account requested another student id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no student matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function show(): void {}
    #[OA\Put(
        path: '/api/v1/students/{student}',
        operationId: 'replaceStudent',
        summary: 'Replace a student',
        description: 'Purpose: replace a student profile; every required field must be supplied. '
            .'Auth: Bearer JWT. Roles: admin, registrar (student accounts receive 403).',
        tags: ['Students'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'student', in: 'path', required: true, description: 'Student id (`students.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Full student payload; the same required fields as `POST /api/v1/students` apply.',
            content: new OA\JsonContent(ref: '#/components/schemas/StudentInput')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Student updated successfully.', content: new OA\JsonContent(ref: '#/components/schemas/StudentItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - the authenticated role may not update students.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no student matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - the student number or user account is already taken.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - required field missing or invalid value.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function replace(): void {}

    #[OA\Patch(
        path: '/api/v1/students/{student}',
        operationId: 'updateStudent',
        summary: 'Update a student',
        description: 'Purpose: partially update a student profile; only the supplied fields are validated and saved. '
            .'Auth: Bearer JWT. Roles: admin, registrar (student accounts receive 403).',
        tags: ['Students'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'student', in: 'path', required: true, description: 'Student id (`students.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Any subset of the student fields, for example `{"first_name": "Janet"}`.',
            content: new OA\JsonContent(ref: '#/components/schemas/StudentInput')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Student updated successfully.', content: new OA\JsonContent(ref: '#/components/schemas/StudentItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - the authenticated role may not update students.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no student matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - the student number or user account is already taken.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - invalid value for a supplied field.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/api/v1/students/{student}',
        operationId: 'deactivateStudent',
        summary: 'Deactivate a student',
        description: 'Purpose: deactivate a student profile; the record is kept and returned with `status: "inactive"`. '
            .'Auth: Bearer JWT. Roles: admin, registrar (student accounts receive 403).',
        tags: ['Students'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'student', in: 'path', required: true, description: 'Student id (`students.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Student deactivated successfully.', content: new OA\JsonContent(ref: '#/components/schemas/StudentItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - the authenticated role may not deactivate students.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no student matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function destroy(): void {}
}
