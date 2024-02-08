<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Jobs\ScanFullSlide;
use App\Models\Scan;
use App\Models\SettingsCategory;
use App\Models\Slide;
use App\Services\SlideScannerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use JsonException;

class ScanController extends Controller
{
//    public function index(Request $request){
//        if($request->has('nth')){
//            $slide = Slide::where('nth',$request->get('nth'))->first();
//            $scan = Scan::where()
//        }
//    }
    private mixed $slideScannerService;

    public function __construct()
    {
        $this->slideScannerService = App::make(SlideScannerService::class);
    }

    /**
     * @throws JsonException
     */
    public function fullSlide(Request $request): JsonResponse|AnonymousResourceCollection
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

            return response()->json(['success' => 'scanning started'], 200);

        }
        return response()->json(['message' => 'Invalid request'], 400);
    }

    public function region(Request $request): JsonResponse|AnonymousResourceCollection
    {
        if ($request->has('selectedRegions') && is_array($request->input('selectedRegions')) && !empty($request->input('selectedRegions'))) {
            $selectedRegions = $request->input('selectedRegions');

            $slideIds = collect($selectedRegions)->pluck('slideId')->all();

            $scans = Scan::where('status', 'ready')
                ->whereIn('id', $slideIds)
                ->with('test.testType')
                ->get();

            if ($scans->isEmpty()) {
                return response()->json(['message' => 'Slides are not ready to scan.'], 404);
            }


            foreach ($scans as $scan) {
                ScanFullSlide::dispatch($scan);
            }

            return response()->json(['success' => 'scanning started'], 200);

        }
        return response()->json(['message' => 'Invalid request'], 400);
    }
}
