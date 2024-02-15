<?php

namespace App\Events;

use App\Models\Scan;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ScanUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private Scan $scan;

    /**
     * Create a new event instance.
     */

    public function __construct(Scan $scan)
    {
        $this->scan = $scan;
    }


    public function broadcastAs(): string
    {
        return 'ScanUpdated';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn(): Channel
    {
        return new Channel('scan.' . $this->scan->id);
    }

    public function broadcastWith(): array
    {
        // Calculate the expected completion time by adding the estimated duration to the updated_at timestamp
        $expectedCompletionTime = $this->scan->updated_at ? $this->scan->updated_at->addSeconds($this->scan->estimated_duration) : null;

        // Calculate seconds left by subtracting the current time from the expected completion time
        $secondsLeft = $this->scan->updated_at ? now()->diffInSeconds($expectedCompletionTime, false) : null; // Use 'false' to allow negative values

        return [
            'slideImage' => $this->scan->slide_image,
            'image' => $this->scan->image,
            'secondsLeft' => $secondsLeft,
            'duration' => $this->scan->duration,
            'progress' => $this->scan->status,
        ];
    }
}
