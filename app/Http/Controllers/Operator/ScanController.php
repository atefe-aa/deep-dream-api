<?php

namespace App\Http\Controllers\Operator;

use App\Events\ScanUpdated;
use App\Helpers\JsonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\scan\RegionScanRequest;
use App\Http\Resources\ScanRequestResource;
use App\Http\Resources\ScanResource;
use App\Jobs\CheckProcessStatusJob;
use App\Models\Region;
use App\Models\Scan;
use App\Models\SettingsCategory;
use App\Models\Slide;
use App\Services\SlideScannerService;
use App\Services\UserNotificationService;
use Exception;
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
    private UserNotificationService $notificationService;

    public function __construct()
    {
        $this->notificationService = new UserNotificationService();
        $this->slideScannerService = App::make(SlideScannerService::class);
    }

    public function processingScans(): JsonResponse|AnonymousResourceCollection
    {
        $scans = Scan::where('is_processing', 1)->get();
        if ($scans) {
            return ScanResource::collection($scans);
        }
        return response()->json(['message' => 'No scan found.']);
    }

    public function nthSlideScan($nthSlide): JsonResponse|ScanResource
    {
        $scan = Scan::where([['nth_slide', $nthSlide], ['is_processing', true]])->first();
        if ($scan) {
            return new ScanResource($scan);
        }
        return response()->json(['message' => 'Scan not found.']);
    }

    public function addTestId(Request $request, $scanId): JsonResponse
    {
        $scan = Scan::findOrFail($scanId);
        $testId = $request->get('testId');
        $slideNumber = $request->get('slideNumber');
        if ($testId && $slideNumber && $scan) {
            if ($scan->test_id && $scan->slide_number) {
                return response()->json(['message' => 'Scan already has a test id.']);
            }
            $scan->update([
                'test_id' => $testId,
                'slide_number' => $slideNumber,
            ]);
            return response()->json(['success' => 'scan updated successfully']);
        }
        return response()->json(['errors' => 'Bad request inputs'], 400);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @throws JsonException
     */
    public function fullSlide(Request $request): JsonResponse
    {
        $scanningInProcess = Scan::where([['status', 'scanning'], ['is_processing', 1]])->get();
        if ($scanningInProcess->count() > 0) {
            return response()->json(['message' => 'The machine is scanning. Please wait till the scanning is finished.'], 500);
        }
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
                // Check if there's an existing scan for the slide with 'ready' status
                $existingScan = Scan::where([['nth_slide', $slide->nth], ['status', 'ready'], ['is_processing', 1]])->first();

                if (!$existingScan) {

                    $scans[] = $slide->toScanArray($slide->nth);
                }
            }

            if (!empty($scans)) {
                Scan::insert($scans);
            }

            $settings = SettingsCategory::query()->MagnificationAndCondenser(1)->get();

            DB::commit();

            $scan = Scan::getFirstStatus('ready');
            if (!$scan) {
                return response()->json(['message' => 'No ready scans found'], 404);
            }

            $coordinates = JsonHelper::decodeJson($scan['slide_coordinates']);
            $scanData = new ScanRequestResource([
                'id' => $scan->id,
                'coordinates' => $coordinates,
                'settings' => $settings
            ]);

            $response = $this->slideScannerService->startScan(["data"=>$scanData->resolve()]);

            if (isset($response['data']) && $response['data']) {

                // $this->notificationService->notifyStatusChange($scan);
                // event(new ScanUpdated($scan));

                $approximateTime = $scan->estimatedDuration();
                Log::info($approximateTime);
                $scan->update([
                    'status' => 'scanning',
                    'estimated_duration' => $approximateTime
                ]);
                // dispatch(new CheckProcessStatusJob($scan))->delay(now()->addSeconds($approximateTime));

                return response()->json(['success' => 'Scanning started']);
            }
            return response()->json(['errors' => 'Scanning failed to start.'], 500);

        } catch (Throwable $e) {
            DB::rollBack();
            Log::error('Failed to start scanning: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json(['message' => 'Creating scans failed. Try again later.'], 500);
        }
    }

    /**
     * @throws JsonException
     */
    public function region(RegionScanRequest $request): JsonResponse
    {
        try {
            $selectedRegions = $request->get('selectedRegions');

            $regionsArray = [];
            foreach ($selectedRegions as $scan) {
                $regionData = [
                    'scan_id' => $scan['scanId'],
                    'status' => 'ready'
                ];
                foreach ($scan['regions'] as $region) {
                    $regionData['coordinates'] = JsonHelper::encodeJson($region);
                    $regionsArray[] = $regionData;
                }
            }
            if (!empty($regionsArray)) {
                Region::insert($regionsArray);
            }

            $regionToScan = Region::where('status', 'ready')->first();
            $settings = $regionToScan->scan->test->testType->settings;

            $coordinates = JsonHelper::decodeJson($regionToScan['coordinates']);
            $scanData = new ScanRequestResource([
                'id' => $regionToScan->id,
                'coordinates' => $coordinates,
                'settings' => $settings,
                'testType' => $regionToScan->scan->test->testType
            ]);

            $response = $this->slideScannerService->startScan($scanData->resolve());

            if (isset($response['success']) && $response['success']) {
                $scan = Scan::where('id', $regionToScan->scan_id)->first();
                $this->notificationService->notifyStatusChange($scan);
                event(new ScanUpdated($scan));

                $approximateTime = $regionToScan->estimatedDuration();
                $regionToScan->update([
                    'status' => 'scanning',
                    'estimated_duration' => $approximateTime
                ]);
                dispatch(new CheckProcessStatusJob($regionToScan))->delay(now()->addSeconds($approximateTime));

                return response()->json(['success' => 'Scanning started']);
            }
            return response()->json(['errors' => 'Scanning failed to start.'], 500);
        } catch (Throwable $e) {

            Log::error('Failed to start scanning region: ' . $e->getMessage(), ['request' => $request->all()]);
            return response()->json(['message' => 'Creating regions failed. Try again later.'], 500);
        }
    }

    public function clearSlots(): JsonResponse
    {
        try {
            $scans = Scan::where('is_processing', 1)->update(['is_processing' => 0]);
            return response()->json(['success' => 'Slots are cleared now.'], 200);
        } catch (Exception $e) {
            Log::info('Slots could not be cleared!' . $e->getMessage());
            return response()->json(['errors' => 'Something went wrong clearing slots.Please try again later.'], 500);

        }
    }
}
