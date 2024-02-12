<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RegionScanned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public int $scanId;
    public string $status;
    public int $currentRegion;
    public int $totalRegions;
    public string $imageUrl;

    /**
     * Create a new event instance.
     */
    public function __construct($scanId, $status, $currentRegion, $totalRegions, $imageUrl = null)
    {
        $this->scanId = $scanId;
        $this->status = $status;
        $this->currentRegion = $currentRegion;
        $this->totalRegions = $totalRegions;
        $this->imageUrl = $imageUrl;
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

    public function broadcastAs(): string
    {
        return 'RegionScanned';
    }

    public function broadcastWith(): array
    {
        return [
            'status' => $this->status,
            'currentRegion' => $this->currentRegion,
            'totalRegions' => $this->totalRegions,
            'imageUrl' => $this->imageUrl,
        ];
    }
}
