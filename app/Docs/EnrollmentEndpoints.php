<?php

namespace App\Docs;

use OpenAPI\Attributes as OA;

/**
 * OpenAPI annotations for the `/api/v1/enrollments` resource and the nested enrollment endpoints.
 */
class EnrollmentEndpoints
{
    #[OA\Get(
        path: '/api/v1/enrollments',
        operationId: 'listEnrollments',
        summary: 'List enrollments',
        description: 'Purpose: browse enrollments; students only receive their own rows and faculty or instructors only the rows of their own offerings. '
            .'Auth: Bearer JWT. Roles: admin, registrar, faculty, instructor, advisor, student.',
        tags: ['Enrollments'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'student_id', in: 'query', description: 'Filter by student id (`students.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
            new OA\Parameter(name: 'course_offering_id', in: 'query', description: 'Filter by course offering id (`course_offerings.id`).', schema: new OA\Schema(type: 'integer'), example: 4),
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string', enum: ['enrolled', 'waitlisted', 'dropped', 'withdrawn', 'completed']), example: 'enrolled'),
            new OA\Parameter(name: 'sort', in: 'query', schema: new OA\Schema(type: 'string', enum: ['enrolled_on', 'status']), example: 'enrolled_on'),
            new OA\Parameter(name: 'direction', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc']), example: 'desc'),
            new OA\Parameter(name: 'per_page', in: 'query', description: 'Page size (1-100), default 20.', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100), example: 20),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Enrollments retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/EnrollmentCollectionResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - the authenticated role may not list enrollments.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - invalid query parameters.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function index(): void {}

    #[OA\Get(
        path: '/api/v1/enrollments/{enrollment}',
        operationId: 'getEnrollment',
        summary: 'Get one enrollment',
        description: 'Purpose: read a single enrollment with its student, course offering and grade. Auth: Bearer JWT. '
            .'Roles: admin, registrar, faculty, instructor, advisor; students may only read their own enrollment and instructors only enrollments of their own offerings.',
        tags: ['Enrollments'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'enrollment', in: 'path', required: true, description: 'Enrollment id (`enrollments.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Enrollment retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/EnrollmentItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - the enrollment belongs to another student or to another instructor.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no enrollment matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function show(): void {}
    #[OA\Post(
        path: '/api/v1/enrollments',
        operationId: 'createEnrollment',
        summary: 'Create an enrollment',
        description: 'Purpose: enroll a student in a course offering; `course_offerings.enrolled_count` is incremented when the status is `enrolled`. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Enrollments'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Enrollment payload. `student_id`, `course_offering_id`, `enrolled_on`, `status` and `grade_mode` are required.',
            content: new OA\JsonContent(ref: '#/components/schemas/EnrollmentInput')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Enrollment created successfully.', content: new OA\JsonContent(ref: '#/components/schemas/EnrollmentItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - "The student is already enrolled in this course offering." (duplicate enrollment).', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - unknown student or course offering, or invalid dates.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function store(): void {}

    #[OA\Put(
        path: '/api/v1/enrollments/{enrollment}',
        operationId: 'replaceEnrollment',
        summary: 'Replace an enrollment',
        description: 'Purpose: replace an enrollment; every required field must be supplied. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Enrollments'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'enrollment', in: 'path', required: true, description: 'Enrollment id (`enrollments.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/EnrollmentInput')),
        responses: [
            new OA\Response(response: 200, description: 'Enrollment updated successfully.', content: new OA\JsonContent(ref: '#/components/schemas/EnrollmentItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no enrollment matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - the student is already enrolled in the target course offering.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - unknown student or course offering, or invalid dates.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function replace(): void {}
    #[OA\Patch(
        path: '/api/v1/enrollments/{enrollment}',
        operationId: 'updateEnrollment',
        summary: 'Update an enrollment',
        description: 'Purpose: partially update an enrollment, for example dropping a course; the `enrolled_count` of the offering is adjusted when the status changes. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Enrollments'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'enrollment', in: 'path', required: true, description: 'Enrollment id (`enrollments.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Any subset of the enrollment fields, for example `{"status": "dropped", "dropped_on": "2026-09-15"}`.',
            content: new OA\JsonContent(ref: '#/components/schemas/EnrollmentInput')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Enrollment updated successfully.', content: new OA\JsonContent(ref: '#/components/schemas/EnrollmentItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no enrollment matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - invalid value for a supplied field.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/api/v1/enrollments/{enrollment}',
        operationId: 'deleteEnrollment',
        summary: 'Delete an enrollment',
        description: 'Purpose: delete an enrollment and return `null` data; `enrolled_count` is decremented first when the enrollment was active. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Enrollments'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'enrollment', in: 'path', required: true, description: 'Enrollment id (`enrollments.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Enrollment deleted successfully.', content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no enrollment matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - a grade still references this enrollment.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function destroy(): void {}
    #[OA\Get(
        path: '/api/v1/students/{student}/enrollments',
        operationId: 'getStudentEnrollments',
        summary: 'List the enrollments of one student',
        description: 'Purpose: read every enrollment of one student, newest first, as a non-paginated list. Auth: Bearer JWT. '
            .'Access is granted to admin and registrar, to the student that owns the record, and to faculty or advisors of an offering the student is enrolled in.',
        tags: ['Enrollments'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'student', in: 'path', required: true, description: 'Student id (`students.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Student enrollments retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/StudentEnrollmentListResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - "You are not authorized to view these enrollments." (URL tampering by a student or an unassigned instructor).', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no student matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function studentEnrollments(): void {}

    #[OA\Get(
        path: '/api/v1/course-offerings/{course_offering}/students',
        operationId: 'getCourseOfferingStudents',
        summary: 'List the students of one course offering',
        description: 'Purpose: roster of the students enrolled in a course offering, ordered by enrollment date. Auth: Bearer JWT. '
            .'Roles: admin, registrar, faculty, instructor, advisor; students receive 403 and instructors may only read their own offerings.',
        tags: ['Enrollments'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'course_offering', in: 'path', required: true, description: 'Course offering id (`course_offerings.id`).', schema: new OA\Schema(type: 'integer'), example: 4),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Course offering students retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/CourseOfferingStudentListResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - "Students cannot view offering rosters." or an instructor that is not assigned to this offering.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no course offering matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function offeringStudents(): void {}
}
