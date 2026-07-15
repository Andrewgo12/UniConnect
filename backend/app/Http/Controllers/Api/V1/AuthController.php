<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService) {}

    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        try {
            $user = $this->authService->register($request->all());
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user' => new UserResource($user->load('profile')),
                'token' => $token,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        try {
            $result = $this->authService->login($request->only('email', 'password'));

            return response()->json([
                'access_token' => $result['token'],
                'token_type' => 'Bearer',
                'expires_at' => $result['expires_at'] ?? null,
                'user' => new UserResource($result['user']->load('profile')),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $this->authService->logout($request->user());

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Get current user
     */
    public function me(Request $request)
    {
        return response()->json(new UserResource($this->authService->getAuthenticatedUser()->load('profile')));
    }

    /**
     * Reset password using email + token (Laravel password broker).
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)]);
        }

        $errors = $status === Password::INVALID_TOKEN
            ? ['token' => [__($status)]]
            : ['email' => [__($status)]];

        return response()->json(['errors' => $errors], 422);
    }
}
