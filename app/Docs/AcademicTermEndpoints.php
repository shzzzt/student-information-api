<?php

namespace App\Docs;

use OpenApi\Attributes as OA;

/**
 * OpenAPI annotations for the `/api/v1/academic-terms` resource. Auth: Bearer JWT, roles admin and registrar.
 */
class AcademicTermEndpoints
{
    #[OA\Get(
        path: '/api/v1/academic-terms',
        operationId: 'listAcademicTerms',
        summary: 'List academic terms',
        description: 'Purpose: browse academic terms with search, filters, sorting and pagination. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Academic Terms'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', description: 'Case-insensitive match on code or name.', schema: new OA\Schema(type: 'string', maxLength: 100), example: '2026'),
            new OA\Parameter(name: 'term_type', in: 'query', schema: new OA\Schema(type: 'string', enum: ['fall', 'spring', 'summer', 'winter', 'intersession']), example: 'fall'),
            new OA\Parameter(name: 'academic_year', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1900, maximum: 2200), example: 2026),
            new OA\Parameter(name: 'status', in: 'query', schema: new OA\Schema(type: 'string', enum: ['planned', 'open', 'closed', 'archived']), example: 'open'),
            new OA\Parameter(name: 'sort', in: 'query', schema: new OA\Schema(type: 'string', enum: ['code', 'academic_year', 'start_date']), example: 'start_date'),
            new OA\Parameter(name: 'direction', in: 'query', schema: new OA\Schema(type: 'string', enum: ['asc', 'desc']), example: 'desc'),
            new OA\Parameter(name: 'per_page', in: 'query', description: 'Page size (1-100), default 20.', schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 100), example: 20),
            new OA\Parameter(name: 'page', in: 'query', schema: new OA\Schema(type: 'integer', minimum: 1), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Academic terms retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/AcademicTermCollectionResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - invalid query parameters.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function index(): void {}

    #[OA\Post(
        path: '/api/v1/academic-terms',
        operationId: 'createAcademicTerm',
        summary: 'Create an academic term',
        description: 'Purpose: create an academic term. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Academic Terms'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Academic term payload. `code`, `name`, `term_type`, `academic_year`, `start_date`, `end_date` and `status` are required.',
            content: new OA\JsonContent(ref: '#/components/schemas/AcademicTermInput')
        ),
        responses: [
            new OA\Response(response: 201, description: 'Academic term created successfully.', content: new OA\JsonContent(ref: '#/components/schemas/AcademicTermItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - the term code already exists.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - duplicate code or invalid dates.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function store(): void {}

    #[OA\Get(
        path: '/api/v1/academic-terms/{academic_term}',
        operationId: 'getAcademicTerm',
        summary: 'Get one academic term',
        description: 'Purpose: read a single academic term. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Academic Terms'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'academic_term', in: 'path', required: true, description: 'Academic term id (`academic_terms.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Academic term retrieved successfully.', content: new OA\JsonContent(ref: '#/components/schemas/AcademicTermItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no academic term matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function show(): void {}
    #[OA\Put(
        path: '/api/v1/academic-terms/{academic_term}',
        operationId: 'replaceAcademicTerm',
        summary: 'Replace an academic term',
        description: 'Purpose: replace an academic term; every required field must be supplied. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Academic Terms'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'academic_term', in: 'path', required: true, description: 'Academic term id (`academic_terms.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(ref: '#/components/schemas/AcademicTermInput')),
        responses: [
            new OA\Response(response: 200, description: 'Academic term updated successfully.', content: new OA\JsonContent(ref: '#/components/schemas/AcademicTermItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no academic term matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - the term code is already used by another term.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - duplicate code or invalid dates.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function replace(): void {}

    #[OA\Patch(
        path: '/api/v1/academic-terms/{academic_term}',
        operationId: 'updateAcademicTerm',
        summary: 'Update an academic term',
        description: 'Purpose: partially update an academic term, for example opening registration. Auth: Bearer JWT. Roles: admin, registrar.',
        tags: ['Academic Terms'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'academic_term', in: 'path', required: true, description: 'Academic term id (`academic_terms.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Any subset of the academic term fields, for example `{"status": "open"}`.',
            content: new OA\JsonContent(ref: '#/components/schemas/AcademicTermInput')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Academic term updated successfully.', content: new OA\JsonContent(ref: '#/components/schemas/AcademicTermItemResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no academic term matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - invalid value for a supplied field.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function update(): void {}

    #[OA\Delete(
        path: '/api/v1/academic-terms/{academic_term}',
        operationId: 'deleteAcademicTerm',
        summary: 'Delete an academic term',
        description: 'Purpose: delete an academic term and return `null` data. Auth: Bearer JWT. Roles: admin, registrar. A term that is still referenced by course offerings is rejected by the database.',
        tags: ['Academic Terms'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'academic_term', in: 'path', required: true, description: 'Academic term id (`academic_terms.id`).', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Academic term deleted successfully.', content: new OA\JsonContent(ref: '#/components/schemas/SuccessResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 403, description: 'Forbidden - role is not admin or registrar.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 404, description: 'Not found - no academic term matches the given id.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 409, description: 'Conflict - course offerings still reference this academic term.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function destroy(): void {}
}
