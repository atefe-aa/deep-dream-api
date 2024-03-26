<?php

namespace App\Services;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MiladService
{
    /**
     * @var Repository|\Illuminate\Contracts\Foundation\Application|Application|mixed
     */
    private mixed $apiKey;
    /**
     * @var Repository|\Illuminate\Contracts\Foundation\Application|Application|mixed
     */
    private mixed $apiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.milad.api_key');
        $this->apiUrl = config('services.milad.api_url');
    }

    public function getAdmitData($admitNumber): array
    {
        $response = Http::withHeaders([
            'apikey' => $this->apiKey,
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => '*/*',
        ])->post($this->apiUrl . '/slider-patient-info-tests', [
            'id' => $admitNumber
        ]);

        if ($response->successful() && $response->body()) {
            Log::info($response->body());
            return ['data' => $response->body()];
        }

        return ['errors' => $response->body()];
    }
}
