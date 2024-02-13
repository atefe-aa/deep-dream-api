<?php

namespace App\Jobs;

use App\Models\Region;
use App\Models\Scan;
use App\Services\SlideScannerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class CheckProcessStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private mixed $data;
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
        $scan = Scan::where('id', $this->data->scanId)->first();
        $region = Region::where('id', $this->data->regionId)->first();
//        if scan or region status is scanning then send the request to the service for the status
        if (($scan && $scan->status === 'scanning') || ($region && $region->status === 'scanning')) {
            $response = $this->slideScannerService->checkStatus($this->data);
            // $response = ['status','image']
            if (!$response['errors']) {

// TODO: broadcast something
            }

// TODO: broadcast something
        }
    }

}
