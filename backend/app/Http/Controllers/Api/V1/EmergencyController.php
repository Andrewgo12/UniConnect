<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmergencyResource;
use App\Models\Emergency;
use App\Services\EmergencyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EmergencyController extends Controller
{
    protected EmergencyService $emergencyService;

    public function __construct(EmergencyService $emergencyService)
    {
        $this->emergencyService = $emergencyService;
    }

    /**
     * Display a listing of emergencies.
     */
    public function index(Request $request)
    {
        $emergencies = $this->emergencyService->listForUser($request->user()->id);

        return EmergencyResource::collection($emergencies);
    }

    /**
     * Store a newly created emergency.
     */
    public function store(Request $request)
    {
        try {
            $emergency = $this->emergencyService->create($request->all(), $request->user());

            return (new EmergencyResource($emergency))->response()->setStatusCode(201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Display the specified emergency.
     */
    public function show(Emergency $emergency)
    {
        $this->authorize('view', $emergency);

        return new EmergencyResource($this->emergencyService->find($emergency));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Emergency $emergency)
    {
        $this->authorize('update', $emergency);

        $validator = Validator::make($request->all(), [
            'status' => 'string|in:active,acknowledged,resolved,cancelled',
            'description' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $updated = $this->emergencyService->update($emergency, $request->all());

            return new EmergencyResource($updated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Emergency $emergency)
    {
        $this->authorize('delete', $emergency);
        $this->emergencyService->delete($emergency);
        return response()->json(null, 204);
    }

    /**
     * Trigger emergency alert
     */
    public function trigger(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:medical,security,help,accident,violence,natural_disaster,technical,other',
            'description' => 'nullable|string',
            'location' => 'nullable',
            'location.lat' => 'nullable|numeric',
            'location.lng' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $emergency = $this->emergencyService->trigger($request->all(), $request->user());

            return response()->json([
                'emergency' => (new EmergencyResource($emergency))->resolve(),
                'message' => 'Emergency alert triggered successfully',
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Get active emergencies
     */
    public function active(Request $request)
    {
        $emergencies = $this->emergencyService->activeForUser($request->user()->id);

        return EmergencyResource::collection($emergencies);
    }
}
