<?php

namespace App\Helpers;

use JsonException;

class JsonHelper
{
    /**
     * @throws JsonException
     */
    public static function decodeJson($jsonString, $asArray = true)
    {
        try {
            return json_decode($jsonString, $asArray, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            // Optionally log the error or handle it as needed
            throw new JsonException('Failed to decode JSON: ' . $e->getMessage());
        }
    }

    /**
     * @throws JsonException
     */
    public static function encodeJson($data): bool|string
    {
        try {
            return json_encode($data, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            // Optionally log the error or handle it as needed
            throw new JsonException('Failed to encode JSON: ' . $e->getMessage());
        }
    }
}
