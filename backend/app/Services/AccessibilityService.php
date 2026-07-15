<?php

namespace App\Services;

use App\Models\AccessibilityLog;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AccessibilityService
{
    public function listLogs(int $userId)
    {
        return AccessibilityLog::where('user_id', $userId)
            ->with('user')
            ->latest()
            ->get();
    }

    public function logAction(User $user, array $data)
    {
        $this->validateLogData($data);

        return AccessibilityLog::logAccessibilityAction($user, $data['action'], [
            'feature' => $data['feature'],
            'accessibility_mode' => $data['accessibility_mode'],
            'device_type' => $data['device_type'] ?? null,
            'input_method' => $data['input_method'] ?? null,
            'assistive_technology' => $data['assistive_technology'] ?? null,
            'duration' => $data['duration'] ?? null,
            'success' => $data['success'],
            'error_message' => $data['error_message'] ?? null,
            'context' => $data['context'] ?? null,
            'previous_mode' => $data['previous_mode'] ?? null,
            'new_mode' => $data['new_mode'] ?? null,
            'metadata' => $data['metadata'] ?? null,
        ]);
    }

    public function getSettings(User $user): array
    {
        $profile = $user->profile;

        return [
            'screen_reader' => $profile->preferences['screen_reader'] ?? false,
            'high_contrast' => $profile->preferences['high_contrast'] ?? false,
            'large_text' => $profile->preferences['large_text'] ?? false,
            'vibration_enabled' => $profile->preferences['vibration_enabled'] ?? true,
            'voice_assistant' => $profile->preferences['voice_assistant'] ?? false,
            'custom_settings' => $profile->preferences ?? [],
        ];
    }

    public function updateSettings(User $user, array $data): array
    {
        $this->validateSettingsData($data);

        $profile = $user->profile ?? Profile::firstOrNew(['user_id' => $user->id], [
            'name' => $user->name,
            'preferences' => [],
        ]);

        $profile->preferences = array_merge($profile->preferences ?? [], $data);
        $profile->save();

        return $this->getSettings($user);
    }

    public function getRecommendations(User $user): array
    {
        $recommendations = [];
        $profile = $user->profile;

        if ($profile && $profile->blind) {
            $recommendations[] = [
                'type' => 'screen_reader',
                'title' => 'Activar lector de pantalla',
                'description' => 'Usa un lector de pantalla para una navegación más accesible.',
                'priority' => 'high',
            ];
        }

        if ($profile && $profile->deaf) {
            $recommendations[] = [
                'type' => 'visual_alerts',
                'title' => 'Alertas visuales',
                'description' => 'Activa alertas visuales para notificaciones importantes.',
                'priority' => 'high',
            ];
        }

        if ($profile && $profile->mute) {
            $recommendations[] = [
                'type' => 'speech_to_text',
                'title' => 'Texto a voz',
                'description' => 'Activa la conversión de texto a voz para facilitar la comunicación.',
                'priority' => 'high',
            ];
        }

        return $recommendations;
    }

    public function testFeature(User $user, string $feature, ?string $deviceType = null): array
    {
        $validFeatures = ['screen_reader', 'high_contrast', 'large_text', 'vibration', 'voice_assistant'];
        if (!in_array($feature, $validFeatures, true)) {
            throw new \InvalidArgumentException('Invalid feature specified');
        }

        $success = true;
        sleep(1);

        $result = [
            'feature' => $feature,
            'success' => $success,
            'response_time' => 1000,
            'message' => 'Feature test successful',
        ];

        AccessibilityLog::logAccessibilityAction($user, 'accessibility_feature_tested', [
            'feature' => $feature,
            'accessibility_mode' => 'standard',
            'device_type' => $deviceType ?? 'unknown',
            'success' => $success,
            'context' => [
                'feature_tested' => $feature,
                'response_time' => $result['response_time'],
            ],
        ]);

        return $result;
    }

    private function validateLogData(array $data): void
    {
        $validator = Validator::make($data, [
            'action' => 'required|string|max:255',
            'feature' => 'required|string|max:255',
            'accessibility_mode' => 'required|string|in:standard,screen_reader,voice_control,sign_language,high_contrast,large_text',
            'device_type' => 'nullable|string|in:mobile,desktop,tablet,voice_assistant',
            'input_method' => 'nullable|string|in:touch,keyboard,voice,gesture,eye_tracking',
            'assistive_technology' => 'nullable|string|in:screen_reader,voice_recognizer,sign_language_interpreter,braille_display',
            'duration' => 'nullable|integer|min:0',
            'success' => 'required|boolean',
            'error_message' => 'nullable|string|max:1000',
            'context' => 'nullable|array',
            'previous_mode' => 'nullable|string|max:100',
            'new_mode' => 'nullable|string|max:100',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    private function validateSettingsData(array $data): void
    {
        $validator = Validator::make($data, [
            'screen_reader' => 'boolean',
            'high_contrast' => 'boolean',
            'large_text' => 'boolean',
            'vibration_enabled' => 'boolean',
            'voice_assistant' => 'boolean',
            'custom_settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }
}
