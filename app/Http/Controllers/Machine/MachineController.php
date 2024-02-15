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
use App\Services\CytomineProjectService;
use Exception;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class MachineController extends Controller
{

    private mixed $cytomineProjectService;
    /**
     * @var Repository|\Illuminate\Contracts\Foundation\Application|Application|mixed
     */
    /**
     * @param CytomineProjectService $cytomineProjectService
     */
    public function __construct(CytomineProjectService $cytomineProjectService)
    {
        $this->cytomineProjectService = $cytomineProjectService;
    }

    /**
     * @param MachineRequest $request
     * @return JsonResponse|ScanRequestResource
     */
    public function scan(MachineRequest $request): JsonResponse|ScanRequestResource
    {
        DB::beginTransaction();
        try {
            $scan = $this->updateScanFromRequest($request);

            if ($request->input('magnification') === 2) {
                $nextScan = $this->prepareNextScan('ready');
                $isRegion = false;
            } else {
                $nextScan = $this->prepareNextScan('2x-scanned');
                $isRegion = true;
            }

            if (!$nextScan) {
                return response()->json('', 404);
            }

            DB::commit();
            return new ScanRequestResource($this->formatScanResponse($nextScan, $isRegion));

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
        $id = $request->input('id');
        $status = $request->input('status');
        //        from the qr code:
        $testId = $request->input('testId');
        $slideNumber = $request->input('slideNumber');
        $magnification = $request->input('magnification');


        if ($magnification === 2) {
            $scan = Scan::findOrFail($id);
            $scanData['status'] = $status;
            if ($status === 'scanned') {
                $scanData['status'] = '2x-scanned';
                $scanData['duration'] = now()->diffInSeconds($scan->updated_at);
            }

            if ($testId) {
                $scanData['test_id'] = $testId;
            }
            if ($slideNumber) {
                $scanData['slide_number'] = $slideNumber;
            }

            $scan->update($scanData);
        } else {
            $region = Region::findOrFail($id);
            $duration = now()->diffInSeconds($region->updated_at);
            $region->update(['status' => $status, 'duration' => $duration]);
            $scan = Scan::where('id', $region->scan_id)->first();
            $this->checkAndFinalizeScan($scan);
        }

        event(new ScanUpdated($scan));
        return $scan;
    }

    /**
     * @param Scan $scan
     * @return void
     */
    private function checkAndFinalizeScan(Scan $scan): void
    {
        $remainingScanRegions = Region::where([['scan_id', $scan->id], ['status', '!=', 'scanned']])->get();

        if ($remainingScanRegions->isEmpty()) {
            $scan->update(['status' => 'scanned']);
        }
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
                ->where([['status', '!=', 'scanned'], ['status', '!=', 'image-ready']])
                ->first();

            if ($region) {
                // If a region is found, use its details for the response
                $settings = $region->scan->test->testType->settings;
                $coordinates = JsonHelper::decodeJson($region['coordinates']);
                $id = $region->id; // Update ID to region's ID if we're dealing with a region
                $testType = $region->scan->test->testType;
                $approximateScanTime = $region->estimated_duration;
                $region->update([
                    'status' => 'scanning',
                    'estimated_duration' => $approximateScanTime
                ]);
                dispatch(new CheckProcessStatusJob($region))->delay(now()->addSeconds($approximateScanTime));
            }
        } else {
            // For a full scan, fetch settings and decode coordinates directly from the scan
            $settings = SettingsCategory::query()->MagnificationAndCondenser(1)->get();
            $coordinates = JsonHelper::decodeJson($scan['slide_coordinates']);
            $approximateScanTime = $scan->estimated_duration;
            $scan->update(['estimated_duration' => $approximateScanTime]);
            dispatch(new CheckProcessStatusJob($scan))->delay(now()->addSeconds($approximateScanTime));
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
     * @param Request $request
     * @return void
     */
    public function image(Request $request): void
    {
        $magnification = $request->get('magnification');
        $id = $request->get('id');
        $image = $request->get('image');
        if ($image) {
            if ($magnification === 2) {
                $scan = Scan::where('id', $id)->first();
                $scan->update([
                    'slide_image' => $image,
                    'status' => '2x-image-ready'
                ]);
            } else {
                $region = Region::where('id', $id)->first();
                if ($region) {
                    $region->update([
                        'image' => $region,
                        'status' => 'image-ready',
                    ]);
                    $scan = Scan::where('id', $region->scan_id)->first();

                    if ($scan) {
                        $scan->update([
                            'status' => 'image-ready'
                        ]);
                        $response = $this->cytomineProjectService->uploadImage($scan->test->project_id, $image);
                        if ($response) {
                            $region->update([
                                'cytomine_image_id' => $response['id'],
                            ]);
                        }
                    }
                }
            }
        }

        event(new ScanUpdated($scan));

    }


}
