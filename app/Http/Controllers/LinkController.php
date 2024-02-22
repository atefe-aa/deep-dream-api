<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\JsonResponse;

class LinkController extends Controller
{
    public function link(string $code): JsonResponse
    {
        $linkUri = Link::where('code', $code)->first();
        if (!$linkUri) {
            return response()->json(['errors' => 'No link found.']);
        }
        $link = $linkUri->uri;
        return response()->json(['data' => $link]);
    }
}

