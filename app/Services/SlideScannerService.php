<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service class for handling project-related operations in Cytomine.
 */
class SlideScannerService
{
    private mixed $apiUrl;

    /**
     * Constructor for CytomineProjectService.
     *
     */
    public function __construct()
    {
        $this->apiUrl = config('services.scanner.api_url');
    }

    public function startScan(array $data): array
    {
        try {

            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->post($this->apiUrl.'/dynamic/scan', $data);

            if ($response->successful()) {
                Log::info($response->json());
                return ['data' => "success"];
            }

            return ['errors' => $response->body()];

        } catch (Exception $e) {
            Log::error("Scanning full slide Failed: {$e->getMessage()}", [$data]);
            return ['errors' => 'Failed to scan slide. Please try again later.'];
        }
    }

    public function checkStatus($data): array
    {
        try {

            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->post($this->apiUrl.'/', $data);

            if ($response->successful()) {
                return ['data' => 'responseData'];
            }
            return ['errors' => 'scanner error'];

        } catch (Exception $e) {
            Log::error("Scanning full slide Failed: {$e->getMessage()}", [$data]);
            return ['errors' => 'Failed to scan slide. Please try again later.'];
        }
    }
}
