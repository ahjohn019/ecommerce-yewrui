<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $auth
    ) {
    }

    /**
     * @OA\Post(
     *     path="/register",
     *     tags={"Auth"},
     *     summary="Register a user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="YOUR_PASSWORD"),
     *             @OA\Property(property="password_confirmation", type="string", example="YOUR_PASSWORD")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->auth->register($request->validated());

        return $this->auth->respond([
            'message' => 'User registered successfully.',
            'data' => [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     tags={"Auth"},
     *     summary="Login and get a token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", example="john@example.com"),
     *             @OA\Property(property="password", type="string", example="YOUR_PASSWORD")
     *         )
     *     ),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->auth->login($request->validated());

        return $this->auth->respond([
            'message' => 'Login successful.',
            'data' => [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/me",
     *     tags={"Auth"},
     *     summary="Get current authenticated user",
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function me(Request $request): JsonResponse
    {
        return $this->auth->respond([
            'data' => new UserResource($request->user()),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/logout",
     *     tags={"Auth"},
     *     summary="Logout the current user",
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function logout(Request $request): JsonResponse
    {
        $this->auth->logout($request->user());

        return $this->auth->respond([
            'message' => 'Logged out successfully.',
        ]);
    }
}
