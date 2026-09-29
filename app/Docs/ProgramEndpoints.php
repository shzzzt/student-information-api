<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

/**
 * OpenAPI annotations for the `/api/v1/programs` resource. Auth: Bearer JWT, roles admin and registrar.
 */
class ProgramEndpoints
{
    #[OA\Get(
        path: '/api/v1/programs',
        operationId: 'listPrograms',
        summary: 'List programs',
        description: 'Purpose: browse academic programs with search, filters, sorting and pagination. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Programs'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', description: 'Case-insensitive match on code, name or department.', schema: new OA\Schema(type: 'string', maxLength: 150), example: 'information'),
            new OA\Parameter(name: 'degree_level', in: 'query', schema: new OA\Schema(type: 'string', enum: ['certificate', 'associate', 'bachelor', 'master', 'doctorate']), example: 'bachelor'),
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string', enum: ['active', 'inactive', 'archived']), example: 'active'),
            new OA\Parameter(name: 'sort', in: 'query', schema: new OA\Schema(type: 'string', enum: ['code', 'name']), example: 'name'),
            new OA\Parameter(name: 'direction', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc']), example: 'asc'),
            new OA\Parameter(name: 'per_page', in: 'query', description: 'Page size (1-100), default 20.', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100), example: 20),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Programs retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/ProgramCollectionResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - invalid query parameters.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function index(): void {}

    #[OA\Post(
        path: '/api/v1/programs',
        operationId: 'createProgram',
        summary: 'Create a program',
        description: 'Purpose: create an academic program. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Programs'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Program payload. `code`, `name`, `degree_level` and `status` are required.',
            content: new OA\JsonContent(ref: '#/components/schemas/ProgramInput')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Program created successfully.', content: new OA\JsonContent(ref: '#/components/schemas/ProgramItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - the program code already exists.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - duplicate code or invalid value.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function store(): void {}

    #[OA\Get(
        path: '/api/v1/programs/{program}',
        operationId: 'getProgram',
        summary: 'Get one program',
        description: 'Purpose: read a single program. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Programs'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'program', in: 'path', required: true, description: 'Program id (`programs.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Program retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/ProgramItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no program matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function show(): void {}
    #[OA\Put(
        path: '/api/v1/programs/{program}',
        operationId: 'replaceProgram',
        summary: 'Replace a program',
        description: 'Purpose: replace a program; every required field must be supplied. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Programs'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'program', in: 'path', required: true, description: 'Program id (`programs.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/ProgramInput')),
        responses: [
            new OA\Response(response: 200, description: 'Program updated successfully.', content: new OA\JsonContent(ref: '#/components/schemas/ProgramItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no program matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - the program code is already used by another program.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - duplicate code or invalid value.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function replace(): void {}

    #[OA\Patch(
        path: '/api/v1/programs/{program}',
        operationId: 'updateProgram',
        summary: 'Update a program',
        description: 'Purpose: partially update a program; only the supplied fields are validated and saved. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Programs'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'program', in: 'path', required: true, description: 'Program id (`programs.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Any subset of the program fields, for example `{"name": "Computer Science"}`.',
            content: new OA\JsonContent(ref: '#/components/schemas/ProgramInput')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Program updated successfully.', content: new OA\JsonContent(ref: '#/components/schemas/ProgramItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no program matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - the program code is already used by another program.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - invalid value for a supplied field.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/api/v1/programs/{program}',
        operationId: 'deleteProgram',
        summary: 'Delete a program',
        description: 'Purpose: delete a program and return `null` data. Auth: Bearer JWT. Roles: admin, registrar. '
            .'A program that is still referenced by students or courses is rejected by the database.',
        tags: ['Programs'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'program', in: 'path', required: true, description: 'Program id (`programs.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Program deleted successfully.', content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no program matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - students or courses still reference this program.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function destroy(): void {}
}