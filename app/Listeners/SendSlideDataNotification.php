<?php

namespace App\Listeners;

use App\Events\ScanUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSlideDataNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ScanUpdated $event): void
    {
        //
    }
}
