<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AnalyticsController extends Controller
{
    protected AnalyticsService $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display analytics dashboard.
     */
    public function index(Request $request)
    {
        return response()->json($this->analyticsService->getDashboardStats($request->user()));
    }

    /**
     * Get message analytics.
     */
    public function messages(Request $request)
    {
        return response()->json($this->analyticsService->getMessageAnalytics($request->user()));
    }

    /**
     * Get emergency analytics.
     */
    public function emergencies(Request $request)
    {
        return response()->json($this->analyticsService->getEmergencyAnalytics($request->user()));
    }

    /**
     * Get accessibility usage analytics.
     */
    public function accessibility(Request $request)
    {
        return response()->json($this->analyticsService->getAccessibilityOverview($request->user()));
    }

    /**
     * Generate analytics report.
     */
    public function generateReport(Request $request)
    {
        try {
            $result = $this->analyticsService->generateReport($request->user(), $request->all());
            return response()->json($result, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Get comprehensive usage analytics.
     */
    public function usage(Request $request)
    {
        try {
            return response()->json($this->analyticsService->getUsageStats($request->all()));
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Get performance analytics.
     */
    public function performance(Request $request)
    {
        try {
            return response()->json($this->analyticsService->getPerformanceStats($request->all()));
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

    /**
     * Track custom analytics event.
     */
    public function track(Request $request)
    {
        try {
            $analytics = $this->analyticsService->trackEvent($request->user(), $request->all());
            return response()->json($analytics, 201);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }

}
