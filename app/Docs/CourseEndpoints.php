<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

/**
 * OpenAPI annotations for the `/api/v1/courses` resource. Auth: Bearer JWT, roles admin and registrar.
 */
class CourseEndpoints
{
    #[OA\Get(
        path: '/api/v1/courses',
        operationId: 'listCourses',
        summary: 'List courses',
        description: 'Purpose: browse courses with search, filters, sorting and pagination. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Courses'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', description: 'Case-insensitive match on course code or title.', schema: new OA\Schema(type: 'string', maxLength: 200), example: 'accounting'),
            new OA\Parameter(name: 'program_id', in: 'query', description: 'Filter by program id (`programs.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string', enum: ['active', 'inactive', 'archived']), example: 'active'),
            new OA\Parameter(name: 'sort', in: 'query', schema: new OA\Schema(type: 'string', enum: ['course_code', 'title']), example: 'course_code'),
            new OA\Parameter(name: 'direction', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc']), example: 'asc'),
            new OA\Parameter(name: 'per_page', in: 'query', description: 'Page size (1-100), default 20.', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100), example: 20),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Courses retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/CourseCollectionResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - invalid query parameters.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function index(): void {}

    #[OA\Post(
        path: '/api/v1/courses',
        operationId: 'createCourse',
        summary: 'Create a course',
        description: 'Purpose: create a course. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Courses'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Course payload. `course_code`, `title` and `status` are required.',
            content: new OA\JsonContent(ref: '#/components/schemas/CourseInput')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Course created successfully.', content: new OA\JsonContent(ref: '#/components/schemas/CourseItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - the course code already exists.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - duplicate code, unknown program or invalid value.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function store(): void {}

    #[OA\Get(
        path: '/api/v1/courses/{course}',
        operationId: 'getCourse',
        summary: 'Get one course',
        description: 'Purpose: read a single course with its program. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Courses'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'course', in: 'path', required: true, description: 'Course id (`courses.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Course retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/CourseItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no course matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function show(): void {}
    #[OA\Put(
        path: '/api/v1/courses/{course}',
        operationId: 'replaceCourse',
        summary: 'Replace a course',
        description: 'Purpose: replace a course; every required field must be supplied. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Courses'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'course', in: 'path', required: true, description: 'Course id (`courses.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CourseInput')),
        responses: [
            new OA\Response(response: 200, description: 'Course updated successfully.', content: new OA\JsonContent(ref: '#/components/schemas/CourseItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no course matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - the course code is already used by another course.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - duplicate code, unknown program or invalid value.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function replace(): void {}

    #[OA\Patch(
        path: '/api/v1/courses/{course}',
        operationId: 'updateCourse',
        summary: 'Update a course',
        description: 'Purpose: partially update a course; only the supplied fields are validated and saved. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Courses'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'course', in: 'path', required: true, description: 'Course id (`courses.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Any subset of the course fields, for example `{"title": "Updated Accounting"}`.',
            content: new OA\JsonContent(ref: '#/components/schemas/CourseInput')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Course updated successfully.', content: new OA\JsonContent(ref: '#/components/schemas/CourseItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no course matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - invalid value for a supplied field.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/api/v1/courses/{course}',
        operationId: 'deleteCourse',
        summary: 'Delete a course',
        description: 'Purpose: delete a course and return `null` data. Auth: Bearer JWT. Roles: admin, registrar. A course that is still referenced by course offerings is rejected by the database.',
        tags: ['Courses'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'course', in: 'path', required: true, description: 'Course id (`courses.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Course deleted successfully.', content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no course matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - course offerings still reference this course.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function destroy(): void {}
}
