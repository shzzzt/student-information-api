<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/v1/auth/login',
        summary: 'Issue a JWT',
        description: 'Purpose: exchange credentials for a JWT. Auth: none - this is the only public endpoint.',
        tags: ['Auth'],
        requestBody: new OA\RequestBody(
            required: true,
            description: 'Credentials of an active user account. `email` and `password` are required.',
            content: new OA\JsonContent(ref: '#/components/schemas/LoginInput')
        ),
        responses: [
            new OA\Response(response: 200, description: 'Authenticated - returns the JWT and its lifetime.', content: new OA\JsonContent(ref: '#/components/schemas/TokenResponse')),
            new OA\Response(response: 401, description: 'Invalid credentials - "Invalid credentials."', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
            new OA\Response(response: 422, description: 'Validation error - email or password missing.', content: new OA\JsonContent(ref: '#/components/schemas/ValidationError')),
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials.'], 401);
        }

        return $this->respondWithToken($token);
    }

    #[OA\Get(
        path: '/api/v1/auth/me',
        summary: 'Get the authenticated user',
        description: 'Purpose: return the authenticated user record (no `data` wrapper). Auth: Bearer JWT, any role.',
        tags: ['Auth'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Current user.', content: new OA\JsonContent(ref: '#/components/schemas/UserResource')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function me(): JsonResponse
    {
        return response()->json(auth('api')->user());
    }

    #[OA\Post(
        path: '/api/v1/auth/logout',
        summary: 'Invalidate the current JWT',
        description: 'Purpose: revoke the presented token so it can no longer be used. No request body is required. Auth: Bearer JWT, any role.',
        tags: ['Auth'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Logged out.', content: new OA\JsonContent(ref: '#/components/schemas/MessageResponse')),
            new OA\Response(response: 401, description: 'Unauthenticated - missing, malformed or expired JWT.', content: new OA\JsonContent(ref: '#/components/schemas/ApiError')),
        ]
    )]
    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out.']);
    }

    private function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }
}
