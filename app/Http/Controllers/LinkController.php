<?php

namespace App\Http\Controllers;

use App\Models\Link;

class LinkController extends Controller
{
    public function link(string $code)
    {
        $linkUri = Link::where('code', $code)->first();
        if (!$linkUri) {
            return response()->json(['errors' => 'No link found.']);
        }
        $link = $linkUri->uri;
        return response()->json(['data' => $link]);
    }
}

