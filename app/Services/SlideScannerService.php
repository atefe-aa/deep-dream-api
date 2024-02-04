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

    /**
     * Creates a new project in Cytomine.
     *
     * @return array An array containing the project ID on success or errors on failure.
     */
    public function scanFullSlide(array $data): array
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
            ])->post($this->apiUrl, $data);

            if ($response->successful()) {
                return ['data' => 'responseData'];
            }
            return ['errors' => 'scanner error'];

        } catch (Exception $e) {
            Log::error("Cytomine Project Creation Failed: {$e->getMessage()}", $data);
            return ['errors' => 'Failed to scan slide. Please try again later.'];
        }
    }
}
