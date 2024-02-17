<?php

namespace App\Services;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;

class SmsService
{
    /**
     * @var Repository|\Illuminate\Contracts\Foundation\Application|Application|mixed
     */
    private mixed $apiKey;
    /**
     * @var Repository|\Illuminate\Contracts\Foundation\Application|Application|mixed
     */
    private mixed $apiUrl;
    /**
     * @var Repository|\Illuminate\Contracts\Foundation\Application|Application|mixed
     */
    private mixed $normalUri;

    public function __construct()
    {
        $this->apiKey = config('services.sms.api_key');
        $this->apiUrl = config('services.sms.api_url');
        $this->normalUri = config('services.sms.normal_uri');
    }

    public function sendNormal($bodyData): array
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'apikey' => $this->apiKey,
        ])->post($this->apiUrl . $this->normalUri, $bodyData);

        if ($response->successful()) {
            return ['data' => $response->json()];
        }

        return ['errors' => $response->body()];
    }
}
