<?php

namespace App\Services;

use App\Models\Analytics;
use App\Models\Emergency;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AnalyticsService
{
    public function getDashboardStats(User $user): array
    {
        $userMessages = Message::where('user_id', $user->id)->count();
        $userEmergencies = Emergency::where('user_id', $user->id)->count();
        $activeConversations = DB::table('conversation_participants')
            ->where('user_id', $user->id)
            ->distinct('conversation_id')
            ->count();

        return [
            'user_stats' => [
                'total_messages' => $userMessages,
                'total_emergencies' => $userEmergencies,
                'active_conversations' => $activeConversations,
                'account_age_days' => $user->created_at->diffInDays(now()),
            ],
            'system_stats' => [
                'total_users' => User::count(),
                'total_messages' => Message::count(),
                'total_emergencies' => Emergency::count(),
            ],
        ];
    }

    public function getMessageAnalytics(User $user)
    {
        return Message::where('user_id', $user->id)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();
    }

    public function getEmergencyAnalytics(User $user)
    {
        return Emergency::where('user_id', $user->id)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();
    }

    public function getAccessibilityAnalytics(User $user)
    {
        return DB::table('messages')
            ->join('phrases', 'messages.type', '=', 'phrases.id')
            ->where('messages.user_id', $user->id)
            ->selectRaw('phrases.category, COUNT(*) as usage_count')
            ->groupBy('phrases.category')
            ->get();
    }

    public function getEmergencyTypeAnalytics(User $user)
    {
        return Emergency::where('user_id', $user->id)
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->get();
    }

    public function getAccessibilityOverview(User $user): array
    {
        $profile = $user->profile;

        return [
            'accessibility_profile' => [
                'blind' => $profile->blind ?? false,
                'deaf' => $profile->deaf ?? false,
                'mute' => $profile->mute ?? false,
            ],
            'phrase_usage' => $this->getAccessibilityAnalytics($user),
            'emergency_triggers' => $this->getEmergencyTypeAnalytics($user),
        ];
    }

    public function trackEvent(User $user, array $data)
    {
        $validator = Validator::make($data, [
            'event_type' => 'required|in:user_action,system_event,medical_event,accessibility_event,emergency_event',
            'category' => 'required|in:usage,performance,security,medical,accessibility,engagement',
            'action' => 'required|string|max:255',
            'resource_type' => 'nullable|string|max:100',
            'resource_id' => 'nullable|integer',
            'value' => 'nullable|numeric',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return Analytics::trackEvent([
            'user_id' => $user->id,
            'event_type' => $data['event_type'],
            'category' => $data['category'],
            'action' => $data['action'],
            'resource_type' => $data['resource_type'] ?? null,
            'resource_id' => $data['resource_id'] ?? null,
            'value' => $data['value'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);
    }

    public function generateReport(User $user, array $data): array
    {
        $this->validateReportData($data);

        $startDate = new \DateTime($data['start_date']);
        $endDate = new \DateTime($data['end_date']);
        $reportType = $data['report_type'];

        $stats = Analytics::getUsageStats($startDate, $endDate);

        $analytics = Analytics::trackEvent([
            'user_id' => $user->id,
            'event_type' => 'system_event',
            'category' => 'analytics',
            'action' => 'report_generated',
            'resource_type' => 'analytics_report',
            'value' => 1,
            'metadata' => [
                'report_type' => $reportType,
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'generated_stats' => $stats,
            ],
        ]);

        return [
            'analytics' => $analytics,
            'stats' => $stats,
        ];
    }

    public function getUsageStats(array $data): array
    {
        $validator = Validator::make($data, [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'period' => 'nullable|in:today,week,month,year',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $startDate = isset($data['start_date']) ? new \DateTime($data['start_date']) : now()->subDays(30);
        $endDate = isset($data['end_date']) ? new \DateTime($data['end_date']) : now();

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'usage_stats' => Analytics::getUsageStats($startDate, $endDate),
            'device_stats' => Analytics::getDeviceStats($startDate, $endDate),
            'accessibility_stats' => Analytics::getAccessibilityStats($startDate, $endDate),
        ];
    }

    public function getPerformanceStats(array $data): array
    {
        $validator = Validator::make($data, [
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $startDate = isset($data['start_date']) ? new \DateTime($data['start_date']) : now()->subDays(7);
        $endDate = isset($data['end_date']) ? new \DateTime($data['end_date']) : now();

        return [
            'performance' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'usage_stats' => Analytics::getUsageStats($startDate, $endDate),
            'device_stats' => Analytics::getDeviceStats($startDate, $endDate),
            'accessibility_stats' => Analytics::getAccessibilityStats($startDate, $endDate),
        ];
    }

    private function validateReportData(array $data): void
    {
        $validator = Validator::make($data, [
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'report_type' => 'required|in:usage,performance,security,medical,accessibility,engagement',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
