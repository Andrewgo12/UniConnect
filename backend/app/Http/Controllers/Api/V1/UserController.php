<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AccessibilityService;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;
    protected AccessibilityService $accessibilityService;

    public function __construct(UserService $userService, AccessibilityService $accessibilityService)
    {
        $this->userService = $userService;
        $this->accessibilityService = $accessibilityService;
    }

    /**
     * Get user profile
     */
    public function show(Request $request)
    {
        return response()->json(
            new \App\Http\Resources\UserResource($this->userService->getProfile($request->user())->load('profile'))
        );
    }

    /**
     * Update user profile
     */
    public function update(Request $request)
    {
        try {
            $updatedUser = $this->userService->updateProfile($request->user(), $request->all());

            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => new \App\Http\Resources\UserResource($updatedUser->load('profile')),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Update accessibility settings
     */
    public function updateAccessibility(Request $request)
    {
        try {
            $settings = $this->accessibilityService->updateSettings($request->user(), $request->all());
            return response()->json($settings);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Get accessibility settings
     */
    public function accessibility(Request $request)
    {
        return response()->json($this->accessibilityService->getSettings($request->user()));
    }
}
