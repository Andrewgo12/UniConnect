<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
{
    protected ImageService $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Display a listing of images.
     */
    public function index(Request $request)
    {
        $images = $this->imageService->listForUser($request->user()->id);
        return response()->json($images);
    }

    /**
     * Store a newly created image.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|image|mimes:jpeg,png,gif,webp|max:10240',
            'type' => 'string|in:profile,sign_language,emergency,medical',
            'description' => 'nullable|string|max:500',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $image = $this->imageService->create($request->all(), $request->user()->id, $request->file('file'));
        return response()->json($image, 201);
    }

    /**
     * Display the specified image.
     */
    public function show(Image $image)
    {
        $this->authorize('view', $image);
        return response()->json($this->imageService->find($image));
    }

    /**
     * Update the specified image.
     */
    public function update(Request $request, Image $image)
    {
        $this->authorize('update', $image);

        $validator = Validator::make($request->all(), [
            'type' => 'string|in:profile,sign_language,emergency,medical',
            'description' => 'nullable|string|max:500',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $updated = $this->imageService->update($image, $request->all());
        return response()->json($updated);
    }

    /**
     * Remove the specified image.
     */
    public function destroy(Image $image)
    {
        $this->authorize('delete', $image);
        $this->imageService->delete($image);
        return response()->json(null, 204);
    }

    /**
     * Get images by type
     */
    public function byType(Request $request, string $type)
    {
        if (! in_array($type, ['profile', 'sign_language', 'emergency', 'medical'], true)) {
            return response()->json(['error' => 'Invalid image type'], 400);
        }

        $images = $this->imageService->listByType($request->user()->id, $type);
        return response()->json($images);
    }

    /**
     * Get profile image
     */
    public function profile(Request $request)
    {
        $profileImage = $this->imageService->getProfileImage($request->user()->id);
        return response()->json($profileImage);
    }

    /**
     * Upload profile image
     */
    public function uploadProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|image|mimes:jpeg,png,gif,webp|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $image = $this->imageService->uploadProfileImage($request->user()->id, $request->file('file'));
        return response()->json($image, 201);
    }
}
