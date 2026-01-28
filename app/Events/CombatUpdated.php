<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CombatUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $combatId
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('combat.' . $this->combatId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'combat.updated';
    }
}
