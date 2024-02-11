<?php

namespace App\Http\Controllers\Machine;

use App\Events\FullSlideScanned;
use App\Http\Controllers\Controller;
use App\Http\Requests\machine\MachineRequest;
use App\Http\Resources\ScanRequestResource;
use App\Models\Scan;
use App\Models\SettingsCategory;
use App\Models\Test;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JsonException;
use function response;

class MachineController extends Controller
{
    /**
     * @throws JsonException
     */
    public function scanStatus(Request $request)
    {
        $validated = $request->validate([
            'scanId' => 'required|integer',
            'status' => 'required|string|in:2xScanned,scanned,failed',
            'imagePath' => 'nullable|string',
            'testId' => 'nullable|integer',
            'slideNumber' => 'nullable|integer',
        ]);

        $scan = Scan::findOrFail($validated['scanId']);
        $scanData = [
            'status' => $validated['status'],
        ];

        switch ($validated['status']) {
            case '2xScanned':
            case 'scanned':
                $field = $validated['status'] === '2xScanned' ? 'slide_image' : 'image';
                $scanData[$field] = $validated['imagePath'];
                // Prepare test data if provided
                $test = !empty($validated['testId']) ? Test::findOrFail($validated['testId']) : [];
                if (!empty($validated['testId'])) {
                    $scanData['test_id'] = $validated['testId'];
                }
                if (!empty($validated['slideNumber'])) {
                    $scanData['slide_number'] = $validated['slideNumber'];
                }
                $scan->update($scanData);
                // Broadcast success with detailed data
                broadcast(new FullSlideScanned(['data' => ['scan' => $scan, 'test' => $test]]));


                break;
            case 'failed':
                // Update scan status to failed
                $scan->update($scanData);
                // Broadcast failure with minimal data including the scan ID
                broadcast(new FullSlideScanned(['error' => 'Scanning Failed', 'scanId' => $validated['scanId']]));
                break;
        }
        if ($validated['status'] === 'scanned') {

        }
        //prepare next scan data
        $settings = SettingsCategory::query()->withMagnificationAndCondenser(1)->get();
        $nextScan = Scan::getFirstReadyScan();
        $coordinates = json_decode($nextScan['slide_coordinates'], true, 512, JSON_THROW_ON_ERROR);
        return new ScanRequestResource(['coordinates' => $coordinates, 'settings' => $settings]);
    }

    /**
     * @throws JsonException
     */
    public function fullSlideScan(MachineRequest $request): JsonResponse|ScanRequestResource
    {
        $scanId = $request->input('scanId');
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

        broadcast(new FullSlideScanned(['data' => ['scan' => $scan, 'test' => $test]]));

        //prepare next scan data
        $nextScan = Scan::getFirstStatus('ready');
        if ($nextScan) {
            $settings = SettingsCategory::query()->withMagnificationAndCondenser(1)->get();
            $nextScan->update(['status' => 'scanning']);
            $coordinates = json_decode($nextScan['slide_coordinates'], true, 512, JSON_THROW_ON_ERROR);
            return new ScanRequestResource(['coordinates' => $coordinates, 'settings' => $settings]);
        }
        return response()->json('', 404);
    }

    public function regionScan(MachineRequest $request): JsonResponse|ScanRequestResource
    {
        $scanId = $request->input('scanId');
        $status = $request->input('status');
        $imagePath = $request->input('imagePath');


        $scan = Scan::findOrFail($scanId);
        $scanData['status'] = $status;

        if ($status === 'scanned') {
            $scanData['image'] = $imagePath;
        }

        $scan->update($scanData);

        broadcast(new FullSlideScanned(['data' => ['scan' => $scan, 'imagePath' => $imagePath]]));

        //prepare next scan data
        $nextScan = Scan::getFirstStatus('2x-scanned');
        if ($nextScan) {
            $settings = SettingsCategory::query()->withMagnificationAndCondenser(1)->get();
            $nextScan->update(['status' => 'scanning']);
            $coordinates = json_decode($nextScan['slide_coordinates'], true, 512, JSON_THROW_ON_ERROR);
            return new ScanRequestResource(['coordinates' => $coordinates, 'settings' => $settings]);
        }
        return response()->json('', 404);
    }


}
