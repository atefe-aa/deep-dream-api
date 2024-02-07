<?php

namespace App\Http\Controllers;

use App\Events\testWebsocket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function testWs(Request $request): JsonResponse
    {
        event(new testWebsocket($request->get("say")));
        return response()->json(['data' => 'now']);
    }

}
