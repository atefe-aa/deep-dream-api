<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FullSlideScanned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */

    public int $data;
    private int $scanId;
    private string $status;
    private string $imageUrl;

    /**
     * @param $scanId
     * @param $status
     * @param $imageUrl
     */
    public function __construct($scanId, $status, $imageUrl = null)
    {
        $this->scanId = $scanId;
        $this->status = $status;
        $this->imageUrl = $imageUrl;
    }


    public function broadcastAs(): string
    {
        return 'FullSlideScanned';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('scan.' . $this->scanId);
    }

    public function broadcastWith(): array
    {
        return [
            'status' => $this->status,
            'imageUrl' => $this->imageUrl,
        ];
    }
}
