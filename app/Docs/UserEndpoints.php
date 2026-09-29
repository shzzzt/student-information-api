<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

/**
 * OpenAPI annotations for the `/api/v1/users` directory endpoint used to populate instructor pickers.
 */
class UserEndpoints
{
    #[OA\Get(
        path: '/api/v1/users',
        operationId: 'listUsers',
        summary: 'List active users',
        description: 'Purpose: directory of active user accounts (id, name, email, role, status) ordered by last name; used to pick instructors and other actors. '
            .'Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Users'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'role', in: 'query', description: 'Optional role filter, for example `faculty`.', schema: new OA\Schema(type: 'string', enum: ['admin', 'registrar', 'faculty', 'instructor', 'advisor', 'student']), example: 'faculty'),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Users retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/UserListResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function index(): void {}
}
