<?php

namespace App\Events;

use App\Models\Emergency;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmergencyTriggered
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Emergency $emergency, public User $user) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->user->id),
            new PrivateChannel('emergency-responders'),
        ];
    }
}
