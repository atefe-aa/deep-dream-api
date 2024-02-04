<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Jobs\ScanFullSlide;
use App\Models\SettingsCategory;
use App\Models\Slide;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function fullSlide(Request $request): JsonResponse
    {
        if ($request->has('slides') && is_array($request->input('slides')) && !empty($request->input('slides'))) {
            $nthSlides = $request->input('slides');

            $slides = Slide::whereIn('nth', $nthSlides)->get();

            if ($slides->isEmpty()) {
                return response()->json(['message' => 'No slides found'], 404);
            }
            $mag2xSettings = SettingsCategory::where('title', '2x')->with('settings')->first();

            foreach ($slides as $slide) {
                ScanFullSlide::dispatch(['slide' => $slide, 'settings' => $mag2xSettings['settings']]);
            }

        }
        return response()->json(['message' => 'Invalid request'], 400);
    }

}
