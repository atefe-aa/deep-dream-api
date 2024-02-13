<?php

namespace App\Http\Controllers\Machine;

use App\Events\ScanUpdated;
use App\Helpers\JsonHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\machine\MachineRequest;
use App\Http\Resources\ScanRequestResource;
use App\Jobs\CheckProcessStatusJob;
use App\Models\Region;
use App\Models\Scan;
use App\Models\SettingsCategory;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class MachineController extends Controller
{
    /**
     * @param MachineRequest $request
     * @return JsonResponse|ScanRequestResource
     */
    public function fullSlideScan(MachineRequest $request): JsonResponse|ScanRequestResource
    {
        DB::beginTransaction();
        try {
            $scan = $this->updateScanFromRequest($request);

            $nextScan = $this->prepareNextScan('ready');
            if ($nextScan) {
                $approximateTime = 20;
                dispatch(new CheckProcessStatusJob($scan))->delay(now()->addMinutes($approximateTime));
                return new ScanRequestResource($this->formatScanResponse($nextScan));
            }
            DB::commit();
            return response()->json('', 404);
        } catch (Exception $e) {
            DB::rollBack();
            Log::info($e);
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    /**
     * @param MachineRequest $request
     * @return Scan
     */
    private function updateScanFromRequest(MachineRequest $request): Scan
    {
        $scanId = $request->input('id');
        $scan = Scan::findOrFail($scanId);

        $status = $request->input('status');
//        from the qr code:
        $testId = $request->input('testId');
        $slideNumber = $request->input('slideNumber');

        $scanData['status'] = $status;

        if ($status === 'scanned') {
            $scanData['status'] = '2x-scanned';
//            TODO : calculate scan duration based on $scan->updated_at and now $scanData['duration'] = $duration
        }

        if ($testId) {
            $scanData['test_id'] = $testId;
        }
        if ($slideNumber) {
            $scanData['slide_number'] = $slideNumber;
        }

        $scan->update($scanData);
        event(new ScanUpdated($scan));
        return $scan;
    }

    /**
     * @param $status
     * @return Scan|null
     */
    private function prepareNextScan($status): ?Scan
    {
        $nextScan = Scan::getFirstStatus($status);
        if ($nextScan) {
            $nextScan->update(['status' => 'scanning']);
            event(new ScanUpdated($nextScan));
            return $nextScan;
        }
        return null;
    }

    /**
     * @param Scan $scan
     * @param bool $isRegion
     * @return array
     * @throws Exception
     */
    private function formatScanResponse(Scan $scan, bool $isRegion = false): array
    {
        // Initialize default values to avoid undefined variable issues
        $settings = [];
        $coordinates = [];
        $testType = [];
        $id = $scan->id; // Default ID is the scan's ID

        if ($isRegion) {
            // Attempt to find the first unscanned region for the given scan
            $region = Region::where('scan_id', $scan->id)
                ->where('status', '!=', 'scanned')
                ->first();

            if ($region) {
                // If a region is found, use its details for the response
                $settings = $region->scan->test->testType->settings;
                $coordinates = JsonHelper::decodeJson($region['coordinates']);
                $id = $region->id; // Update ID to region's ID if we're dealing with a region
                $testType = $region->scan->test->testType;
            }
        } else {
            // For a full scan, fetch settings and decode coordinates directly from the scan
            $settings = SettingsCategory::query()->MagnificationAndCondenser(1)->get();
            Log::info($settings);
            $coordinates = JsonHelper::decodeJson($scan['slide_coordinates']);
        }

        // Return a structured response
        return [
            'id' => $id,
            'coordinates' => $coordinates,
            'settings' => $settings,
            'testType' => $testType
        ];
    }

    /**
     * @param MachineRequest $request
     * @return JsonResponse|ScanRequestResource
     */
    public function regionScan(MachineRequest $request): JsonResponse|ScanRequestResource
    {
        DB::beginTransaction();
        try {
            $region = Region::findOrFail($request->input('id'));
            $status = $request->input('status');

            $region->update(['status' => $status]);
            $scan = Scan::where('id', $region->scan_id)->first();
            event(new ScanUpdated($scan));

            $this->checkAndFinalizeScan($region);

            $nextScan = $this->prepareNextScan('2x-scanned');
            DB::commit();
            if ($nextScan) {
                //                TODO: schedule a time to check for the response
                $approximateTime = 20;
                dispatch(new CheckProcessStatusJob($nextScan))->delay(now()->addMinutes($approximateTime));
                return new ScanRequestResource($this->formatScanResponse($nextScan, true));
            }

            return response()->json('', 404);

        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'An unexpected error occurred'], 500);
        }
    }

    /**
     * @param Region $region
     * @return void
     */
    private function checkAndFinalizeScan(Region $region): void
    {
        // Check if all regions of the slide have been scanned
        // Update scan status to 'scanned' if true
        // Optionally, calculate duration and update it
        $scan = Scan::where('id', $region->scan_id)->first();
        $remainingScanRegions = Region::where([['scan_id', $scan->id], ['status', '!=', 'scanned']])->get();

        if ($remainingScanRegions->isEmpty()) {
            $scan->update(['status' => 'scanned']);
            // TODO: Implement duration calculation and broadcasting
        }
    }

    /**
     * @param Request $request
     * @return void
     */
    public function image(Request $request): void
    {
        $magnification = $request->get('magnification');
        $id = $request->get('id');
        $image = $request->get('image');
        if ($magnification === '2x') {
            $scan = Scan::where('id', $id)->first();
            $scan->update([
                'slide_image' => $image
            ]);

        } else {
            $region = Region::where('id', $id)->update([
                'image' => $image
            ]);
            $scan = Scan::where('id', $region->scan_id)->first();
        }
        event(new ScanUpdated($scan));
        //            TODO : broadcast the image or the exist of the image
//        TODO: calculate the scan duration based on updated_at and now
    }

}
