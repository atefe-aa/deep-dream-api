<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Http\Resources\ScanRequestResource;
use App\Jobs\ScanRegion;
use App\Models\Scan;
use App\Models\Setting;
use App\Models\SettingsCategory;
use App\Models\Slide;
use App\Services\SlideScannerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use JsonException;
use Throwable;

class ScanController extends Controller
{
    private mixed $slideScannerService;

    public function __construct()
    {
        $this->slideScannerService = App::make(SlideScannerService::class);
    }

    /**
     * @throws JsonException
     */
    public function fullSlide(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'slides' => 'required|array|min:1',
        ]);

        $nthSlides = $validated['slides'];

        DB::beginTransaction();

        try {
            $slides = Slide::whereIn('nth', $nthSlides)->get();

            if ($slides->isEmpty()) {
                DB::rollBack();
                return response()->json(['message' => 'No slides found'], 404);
            }

            $scans = [];
            foreach ($slides as $slide) {
                $scans[] = $slide->toScanArray();
            }

            if (!$scans) {
                DB::rollBack();
                return response()->json(['message' => 'Failed to create scans.'], 500);
            }

            $settings = SettingsCategory::query()->withMagnificationAndCondenser(1)->get();

            DB::commit();

            $scan = Scan::getFirstReadyScan();
            $scanData = new ScanRequestResource(['scan' => $scan, 'settings' => $settings]);

            $response = $this->slideScannerService->scanFullSlide($scanData->resolve());

            if (isset($response['success']) && $response['success']) {
                return response()->json(['success' => 'Scanning started']);
            }
            return response()->json(['errors' => 'Scanning failed to start.']);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Failed to start scanning: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json(['message' => 'Creating scans failed. Try again later.'], 500);
        }
    }

    public function region(Request $request): JsonResponse|AnonymousResourceCollection
    {
        if ($request->has('selectedRegions') && is_array($request->input('selectedRegions')) && !empty($request->input('selectedRegions'))) {
            $selectedRegions = $request->input('selectedRegions');

            $scanIds = collect($selectedRegions)->pluck('scanId')->all();

            $scans = Scan::where('status', 'ready')
                ->whereIn('id', $scanIds)
                ->with('test.testType')
                ->get();

            if ($scans->isEmpty()) {
                return response()->json(['message' => 'Slides are not ready to scan.'], 404);
            }


            foreach ($scans as $scan) {
                $regions = [];

                $settings = Setting::where('category_id', $scan['test']['testType']['magnification'])->get();
                foreach ($selectedRegions as $selectedRegion) {
                    $regions = $selectedRegion['scanId'] === $scan->id ?? $selectedRegion['regions'];
                }
                if (!empty($regions)) {
                    foreach ($regions as $region) {
                        $scanData = [
                            'region' => $region,
                            'settings' => $settings,
                        ];
                        ScanRegion::dispatch($scanData);
                    }
                }
            }

            return response()->json(['success' => 'scanning started'], 200);

        }
        return response()->json(['message' => 'Invalid request'], 400);
    }
}
