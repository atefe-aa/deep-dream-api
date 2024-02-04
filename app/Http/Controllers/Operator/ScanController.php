<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScanResource;
use App\Models\Scan;
use App\Models\SettingsCategory;
use App\Models\Slide;
use App\Models\Test;
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
            $scansArray = [];
            foreach ($slides as $slide) {
//                ScanFullSlide::dispatch(['slide' => $slide, 'settings' => $mag2xSettings['settings']]);
//                $response = $this->slideScannerService->scanFullSlide($this->data);
                // $response = ['test_id','slide_number','slide_image']
//                if (!$response['errors']) {
//                    $test = Test::where('id', $response['test_id'])->first();
                $test = Test::where('id', 1)->with(['testType', 'laboratory'])->first();

                $scan = Scan::create([
                    'test_id' => 1 + $slide->nth,
//                        'test_id' => $response['test_id'],
                    'slide_number' => 1,
//                        'slide_number' => $response['slide_number'],
                    'slide_coordinates' => json_encode([
                        'sw' =>
                            ['x' => $slide->sw_x, 'y' => $slide->sw_y],
                        'ne' =>
                            ['x' => $slide->ne_x, 'y' => $slide->ne_y]
                    ], JSON_THROW_ON_ERROR),
                    'slide_image' => "/media/slides/slide1.png"
                ]);
                $scansArray[] = ['scan' => $scan, 'test' => $test, 'nth' => $slide->nth];
//                }
            }
            return ScanResource::collection($scansArray);

        }
        return response()->json(['message' => 'Invalid request'], 400);
    }

}
