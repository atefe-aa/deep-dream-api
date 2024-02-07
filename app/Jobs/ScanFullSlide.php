<?php

namespace App\Jobs;

use App\Events\FullSlideScanned;
use App\Models\Scan;
use App\Models\Test;
use App\Services\SlideScannerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class ScanFullSlide implements ShouldQueue
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
        $response = $this->slideScannerService->scanFullSlide($this->data);
        // $response = ['test_id','slide_number','slide_image']
        if (!$response['errors']) {
            $test = Test::where('id', $response['test_id'])->first();

            $scan = Scan::create([
                'test_id' => $response['test_id'],
                'slide_number' => $response['slide_number'],
                'slide_coordinates' => [
                    'sw' =>
                        ['x' => $this->data['slide']['sw_x'], 'y' => $this->data['slide']['sw_y']],
                    'ne' =>
                        ['x' => $this->data['slide']['ne_x'], 'y' => $this->data['slide']['ne_y']]
                ],
                'slide_image' => $response['slide_image']
            ]);
            broadcast(new FullSlideScanned(['data' => ['scan' => $scan, 'test' => $test], 'nth' => $this->data['slide']['nth']]));
        }

        broadcast(new FullSlideScanned(['error' => 'Scanning Failed ' . $this->data['slide']['nth'], 'nth' => $this->data['slide']['nth']]));
    }
}
