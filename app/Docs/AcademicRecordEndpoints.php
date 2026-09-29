<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

/**
 * OpenAPI annotations for the academic record endpoint.
 */
class AcademicRecordEndpoints
{
    #[OA\Get(
        path: '/api/v1/students/{student}/academic-record',
        operationId: 'getStudentAcademicRecord',
        summary: 'Get a student academic record',
        description: 'Purpose: return the enrollments of one student grouped by academic term, each with its course offering and grade. Auth: Bearer JWT. '
            .'Access is granted to admin and registrar, to the student that owns the record, and to faculty or advisors of an offering the student is enrolled in.',
        tags: ['Academic Record'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'student', in: 'path', required: true, description: 'Student id (`students.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Academic record retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/AcademicRecordResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - another student record was requested or the role is not allowed.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no student matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function show(): void {}
}
