<?php

namespace App\Observers;

use App\Models\Emergency;
use Illuminate\Support\Facades\Log;

class EmergencyObserver
{
    public function created(Emergency $emergency): void
    {
        Log::warning('Emergency created', [
            'emergency_id' => $emergency->id,
            'type'         => $emergency->type,
            'user_id'      => $emergency->user_id,
        ]);
    }

    public function updated(Emergency $emergency): void
    {
        if ($emergency->isDirty('status') && $emergency->status === 'resolved') {
            Log::info('Emergency resolved', ['emergency_id' => $emergency->id]);
        }
    }

    public function deleted(Emergency $emergency): void
    {
        Log::info('Emergency deleted', ['emergency_id' => $emergency->id]);
    }
}
