<?php

namespace App\Services;

use App\Helpers\JsonHelper;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
        Log::info($url);
        $data = JsonHelper::encodeJson([
            "title" => $title,
            "url" => $url,
        ]);
//        $ch = curl_init($this->apiUrl);
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, [
//            'Content-Type: application/json',
//            'X-API-Key: ' . $this->apiKey,
//            'Content-Length: ' . strlen($data)
//        ]);

//        $result = curl_exec($ch);
//        if ($result === false) {
//            throw new Exception('cURL Error: ' . curl_error($ch));
//        }
//        curl_close($ch);
//        return JsonHelper::decodeJson($result);

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
