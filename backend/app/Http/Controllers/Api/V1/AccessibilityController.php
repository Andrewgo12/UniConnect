<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AccessibilityLog;
use App\Services\AccessibilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AccessibilityController extends Controller
{
    protected AccessibilityService $accessibilityService;

    public function __construct(AccessibilityService $accessibilityService)
    {
        $this->accessibilityService = $accessibilityService;
    }

    /**
     * Display accessibility logs for user.
     */
    public function index(Request $request)
    {
        $logs = $this->accessibilityService->listLogs($request->user()->id);
        return response()->json($logs);
    }

    /**
     * Store accessibility log entry.
     */
    public function store(Request $request)
    {
        try {
            $log = $this->accessibilityService->logAction($request->user(), $request->all());
            return response()->json($log, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Display the specified accessibility log.
     */
    public function show(AccessibilityLog $accessibilityLog)
    {
        $this->authorize('view', $accessibilityLog);
        
        $accessibilityLog->load('user');
        return response()->json($accessibilityLog);
    }

    /**
     * Update the specified accessibility log.
     */
    public function update(Request $request, AccessibilityLog $accessibilityLog)
    {
        $this->authorize('view', $accessibilityLog);

        $validator = Validator::make($request->all(), [
            'action'               => 'string|max:255',
            'feature'              => 'string|max:255',
            'accessibility_mode'   => 'string|in:standard,screen_reader,voice_control,sign_language,high_contrast,large_text',
            'success'              => 'boolean',
            'error_message'        => 'nullable|string|max:1000',
            'context'              => 'nullable|array',
            'metadata'             => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $accessibilityLog->update($request->only([
            'action', 'feature', 'accessibility_mode',
            'success', 'error_message', 'context', 'metadata',
        ]));

        return response()->json($accessibilityLog);
    }

    /**
     * Remove the specified accessibility log.
     */
    public function destroy(AccessibilityLog $accessibilityLog)
    {
        $this->authorize('view', $accessibilityLog);
        $accessibilityLog->delete();
        return response()->json(null, 204);
    }

    /**
     * Get accessibility settings for user.
     */
    public function settings(Request $request)
    {
        return response()->json($this->accessibilityService->getSettings($request->user()));
    }

    /**
     * Update accessibility settings.
     */
    public function updateSettings(Request $request)
    {
        try {
            $settings = $this->accessibilityService->updateSettings($request->user(), $request->all());
            return response()->json($settings);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Get accessibility recommendations.
     */
    public function recommendations(Request $request)
    {
        return response()->json($this->accessibilityService->getRecommendations($request->user()));
    }

    /**
     * Test accessibility features.
     */
    public function test(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'feature' => 'required|string|in:screen_reader,high_contrast,large_text,vibration,voice_assistant',
            'device_type' => 'nullable|string|in:mobile,desktop,tablet,voice_assistant',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = $this->accessibilityService->testFeature($request->user(), $request->feature, $request->device_type);
            return response()->json($result);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
