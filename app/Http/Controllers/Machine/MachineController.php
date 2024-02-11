<?php

namespace App\Http\Controllers\Machine;

use App\Http\Controllers\Controller;
use App\Http\Requests\machine\MachineRequest;
use App\Http\Resources\ScanRequestResource;
use App\Models\Region;
use App\Models\Scan;
use App\Models\SettingsCategory;
use App\Models\Test;
use Illuminate\Http\JsonResponse;
use JsonException;
use function response;

class MachineController extends Controller
{

    /**
     * @throws JsonException
     */
    public function fullSlideScan(MachineRequest $request): JsonResponse|ScanRequestResource
    {
        $scanId = $request->input('id');
        $status = $request->input('status');
        $imagePath = $request->input('imagePath');

//        from the qr code:
        $testId = $request->input('testId');
        $slideNumber = $request->input('slideNumber');

        $scan = Scan::findOrFail($scanId);
        $scanData['status'] = $status;

        if ($status === 'scanned') {
            $scanData['slide_image'] = $imagePath;
            $scanData['status'] = '2x-scanned';
        }

        $test = $testId ? Test::findOrFail($testId) : [];
        if ($testId) {
            $scanData['test_id'] = $testId;
        }
        if ($slideNumber) {
            $scanData['slide_number'] = $slideNumber;
        }

        $scan->update($scanData);

//        broadcast(new FullSlideScanned(['data' => ['scan' => $scan, 'test' => $test]]));

        //prepare next scan data
        $nextScan = Scan::getFirstStatus('ready');
        if ($nextScan) {
            $settings = SettingsCategory::query()->withMagnificationAndCondenser(1)->get();
            $nextScan->update(['status' => 'scanning']);
            $coordinates = json_decode($nextScan['slide_coordinates'], true, 512, JSON_THROW_ON_ERROR);
            return new ScanRequestResource(['id' => $nextScan->id, 'coordinates' => $coordinates, 'settings' => $settings]);
        }
        return response()->json('', 404);
    }

    /**
     * @throws JsonException
     */
    public function regionScan(MachineRequest $request): JsonResponse|ScanRequestResource
    {
        $regionId = $request->input('id');
        $status = $request->input('status');
        $imagePath = $request->input('imagePath');

        $region = Region::findOrFail($regionId);
        $regionData['status'] = $status;

        if ($status === 'scanned') {
            $regionData['image'] = $imagePath;
        }

        $region->update($regionData);

//        broadcast(new FullSlideScanned(['data' => ['region' => $region, 'imagePath' => $imagePath]]));

        //prepare next scan data
        $scan = Scan::where('id', $region->scan_id)->first();
        $remainingScanRegions = Region::where([['scan_id', $scan->id], ['status', '!=', 'scanned']])->get();

//        check if all the regions of the slide have been scanned and set the scan status to 'scanned'
        if ($remainingScanRegions->count() === 0) {
            $scan->update(['status' => 'scanned']);
        }

        $nextScan = Scan::getFirstStatus('2x-scanned');
        if ($nextScan) {
            $region = Region::where([['scan_id', $nextScan->id], ['status', '!=', 'scanned']])->first();
            if ($region) {
                $settings = SettingsCategory::query()->withMagnificationAndCondenser(1)->get();
                $nextScan->update(['status' => 'scanning']);
                $coordinates = json_decode($region->coordinates, true, 512, JSON_THROW_ON_ERROR);
                return new ScanRequestResource(['coordinates' => $coordinates, 'settings' => $settings]);
            }
            return response()->json('', 404);
        }
        return response()->json('', 404);
    }


}
