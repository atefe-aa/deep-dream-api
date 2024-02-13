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
        return [
            'id' => $this->scan->id,
            'nth' => $this->scan->nth_slide,
            'slideImage' => $this->scan->slide_image,
            'slideNumber' => $this->scan->slide_number,
            'image' => $this->scan->image,
            'laboratory' => $this->scan->test ? $this->scan->test->laboratory->title : null,
            'testNumber' => $this->scan->test ? $this->scan->test->id : null,
            'testType' => $this->scan->test ? $this->scan->test->testType->title : null,
            'duration' => $this->scan->duration,
            'progress' => $this->scan->status,
        ];
    }
}
