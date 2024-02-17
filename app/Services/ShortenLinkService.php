<?php

namespace App\Services;

use App\Helpers\JsonHelper;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use JsonException;

class YunService
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
        $this->apiKey = config('services.yun.api_key');
        $this->apiUrl = config('services.yun.api_url');
    }

    /**
     * @throws JsonException
     */
    public function shortUrl($title, $url): array
    {
        $data = JsonHelper::encodeJson([
            "title" => $title,
            "url" => $url,
        ]);

        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-API-Key' => $this->apiKey,
            'Content-Length' => strlen($data)
        ])->post($this->apiUrl, $data);

        if ($response->successful()) {
            return ['data' => $response->json()];
        }

        return ['errors' => $response->body()];
    }
}
