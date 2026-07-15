<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SecurityAlert
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $alertType,
        public string $message,
        public User $user,
        public array $metadata = []
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.' . $this->user->id),
            new PrivateChannel('security-admins'),
        ];
    }
}
