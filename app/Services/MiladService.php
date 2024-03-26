<?php

namespace App\Services;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;

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
        $response = Http::withoutVerifying()->withHeaders([
            'apikey' => $this->apiKey,
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => '*/*',
        ])->asForm()->post($this->apiUrl . '/slider-patient-info-tests', [
            'id' => $admitNumber
        ]);

        if ($response->successful() && $response->json()) {
            return ['data' => $response->json()];
        }
        return ['errors' => $response->body()];
    }
}
