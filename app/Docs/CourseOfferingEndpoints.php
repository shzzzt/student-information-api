<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

/**
 * OpenAPI annotations for the `/api/v1/course-offerings` resource.
 */
class CourseOfferingEndpoints
{
    #[OA\Get(
        path: '/api/v1/course-offerings',
        operationId: 'listCourseOfferings',
        summary: 'List course offerings',
        description: 'Purpose: browse the classes offered for an academic term. Auth: Bearer JWT. Roles: admin, registrar, faculty, instructor, advisor. '
            .'A student account, and faculty or instructors of other offerings, receive 403; faculty and instructors only see their own offerings.',
        tags: ['Course Offerings'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', description: 'Case-insensitive match on the course code or title.', schema: new OA\Schema(type: 'string', maxLength: 200), example: 'programming'),
            new OA\Parameter(name: 'course_id', in: 'query', description: 'Filter by course id (`courses.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
            new OA\Parameter(name: 'academic_term_id', in: 'query', description: 'Filter by academic term id (`academic_terms.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
            new OA\Parameter(name: 'instructor_id', in: 'query', description: 'Filter by instructor user id (`users.id`).', schema: new OA\Schema(type: 'integer'), example: 3),
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string', enum: ['scheduled', 'open', 'closed', 'cancelled', 'completed']), example: 'open'),
            new OA\Parameter(name: 'sort', in: 'query', schema: new OA\Schema(type: 'string', enum: ['section', 'capacity', 'status']), example: 'section'),
            new OA\Parameter(name: 'direction', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc']), example: 'asc'),
            new OA\Parameter(name: 'per_page', in: 'query', description: 'Page size (1-100), default 20.', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100), example: 20),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Course offerings retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/CourseOfferingCollectionResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - student account or role not allowed to browse offerings.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - invalid query parameters.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function index(): void {}

    #[OA\Get(
        path: '/api/v1/course-offerings/{course_offering}',
        operationId: 'getCourseOffering',
        summary: 'Get one course offering',
        description: 'Purpose: read a single course offering with its course, term and instructor. Auth: Bearer JWT. '
            .'Roles: admin, registrar, faculty, instructor, advisor; students receive 403 and instructors may only read their own offerings.',
        tags: ['Course Offerings'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'course_offering', in: 'path', required: true, description: 'Course offering id (`course_offerings.id`).', schema: new OA\Schema(type: 'integer'), example: 4),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Course offering retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/CourseOfferingItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - student account or an instructor that is not assigned to this offering.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no course offering matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function show(): void {}
    #[OA\Post(
        path: '/api/v1/course-offerings',
        operationId: 'createCourseOffering',
        summary: 'Create a course offering',
        description: 'Purpose: schedule a section of a course for an academic term. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Course Offerings'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Course offering payload. `course_id`, `academic_term_id`, `instructor_id`, `section`, `day_of_week`, `start_time`, `end_time`, `delivery_mode`, `capacity` and `status` are required.',
            content: new OA\JsonContent(ref: '#/components/schemas/CourseOfferingInput')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Course offering created successfully.', content: new OA\JsonContent(ref: '#/components/schemas/CourseOfferingItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - the same course, term and section combination already exists.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - unknown course or term, instructor without a teaching role, or invalid schedule.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function store(): void {}

    #[OA\Put(
        path: '/api/v1/course-offerings/{course_offering}',
        operationId: 'replaceCourseOffering',
        summary: 'Replace a course offering',
        description: 'Purpose: replace a course offering; every required field must be supplied. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Course Offerings'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'course_offering', in: 'path', required: true, description: 'Course offering id (`course_offerings.id`).', schema: new OA\Schema(type: 'integer'), example: 4),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/CourseOfferingInput')),
        responses: [
            new OA\Response(response: 200, description: 'Course offering updated successfully.', content: new OA\JsonContent(ref: '#/components/schemas/CourseOfferingItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no course offering matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - the same course, term and section combination already exists.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - unknown course or term, instructor without a teaching role, or invalid schedule.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function replace(): void {}
    #[OA\Patch(
        path: '/api/v1/course-offerings/{course_offering}',
        operationId: 'updateCourseOffering',
        summary: 'Update a course offering',
        description: 'Purpose: partially update a course offering, for example closing it or moving the room. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Course Offerings'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'course_offering', in: 'path', required: true, description: 'Course offering id (`course_offerings.id`).', schema: new OA\Schema(type: 'integer'), example: 4),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Any subset of the course offering fields, for example `{"room": "CCS 305", "status": "closed"}`.',
            content: new OA\JsonContent(ref: '#/components/schemas/CourseOfferingInput')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Course offering updated successfully.', content: new OA\JsonContent(ref: '#/components/schemas/CourseOfferingItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no course offering matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - invalid value for a supplied field.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/api/v1/course-offerings/{course_offering}',
        operationId: 'deleteCourseOffering',
        summary: 'Delete a course offering',
        description: 'Purpose: delete a course offering and return `null` data. Auth: Bearer JWT. Roles: admin, registrar. A course offering that still has enrollments is rejected by the database.',
        tags: ['Course Offerings'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'course_offering', in: 'path', required: true, description: 'Course offering id (`course_offerings.id`).', schema: new OA\Schema(type: 'integer'), example: 4),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Course offering deleted successfully.', content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no course offering matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - enrollments still reference this course offering.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function destroy(): void {}
}
