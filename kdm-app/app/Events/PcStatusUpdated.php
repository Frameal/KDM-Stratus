<?php

namespace App\Events;

use App\Models\Pc;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// "ShouldBroadcastNow" tells Laravel to push this instantly without waiting in a queue
class PcStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pc;

    public function __construct(Pc $pc)
    {
        $this->pc = $pc;
    }

    // We broadcast to a specific branch channel so other branches don't get the update
    public function broadcastOn(): array
    {
        return [
            new Channel('branch.' . $this->pc->branch_id)
        ];
    }
}