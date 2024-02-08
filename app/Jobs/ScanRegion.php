<?php

namespace App\Jobs;

use App\Events\FullSlideScanned;
use App\Events\RegionScanned;
use App\Models\Scan;
use App\Services\SlideScannerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class ScanRegion implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public mixed $data;
    private SlideScannerService $slideScannerService;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->slideScannerService = App::make(SlideScannerService::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $response = $this->slideScannerService->scanRegion($this->data);
        // $response = ['scan_id','image']
        if (!$response['errors']) {
            $scan = Scan::find($this->data->id);
            $scan->update([
                'image' => $response['image']
            ]);
            broadcast(new RegionScanned(['data' => ['scan' => $scan]]));
        }

        broadcast(new FullSlideScanned(['error' => 'Scanning Failed ' . $this->data['scan']['id']]));
    }
}
