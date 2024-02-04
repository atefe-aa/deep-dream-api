<?php

namespace App\Listeners;

use App\Events\FullSlideScanned;
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
    public function handle(FullSlideScanned $event): void
    {
        //
    }
}
