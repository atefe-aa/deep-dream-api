<?php

namespace App\Http\Resources;

use App\Helpers\JsonHelper;
use App\Models\Region;
use App\Models\Scan;
use App\Models\SettingsCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScanRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $minCondenser =
        $maxCondenser =
        $defaultCondenser =
        $minFocus =
        $maxFocus =
        $magnificationPosition =
        $xStep =
        $yStep =
        $zStep =
        $numLayer =
        $numMergeLayer =
        $mergeAlgorithm =
        $stitchAlgorithm =
            null;

        foreach ($this['settings'] as $category) {
            foreach ($category['settings'] as $setting) {
                switch ($setting['key']) {
                    case 'min':
                        if ($category['title'] === 'condenser') {
                            $minCondenser = $setting['value'];
                        }
                        break;
                    case 'max':
                        if ($category['title'] === 'condenser') {
                            $maxCondenser = $setting['value'];
                        }
                        break;
                    case 'condenser':
                        $defaultCondenser = $this['testType']['condenser'] ?? $setting['value'];
                        break;
                    case 'min-focus':
                        $minFocus = $setting['value'];
                        break;
                    case 'max-focus':
                        $maxFocus = $setting['value'];
                        break;
                    case 'placement':
                        $magnificationPosition = $setting['value'];
                        break;
                    case 'x':
                        $xStep = $setting['value'];
                        break;
                    case 'y':
                        $yStep = $setting['value'];
                        break;
                    case 'z':
                        $zStep = isset(['testType']['z_axis'])
                            ? $this['testType']['z_axis']
                            : $setting['value'];
                        break;
                    case 'number-of-layers':
                        $numLayer = $setting['value'];
                        break;
                    case 'number-of-merge-layers':
                        $numMergeLayer = $setting['value'];
                        break;
                    case 'stitch-algorithm':
                        $stitchAlgorithm = $setting['value'];
                        break;
                    case 'merge-algorithm':
                        $mergeAlgorithm = $setting['value'];
                        break;
                }
            }
        }

        $minX = $this['coordinates'] ? $this['coordinates']['sw']['x'] : null;
        $minY = $this['coordinates'] ? $this['coordinates']['sw']['y'] : null;
        $maxX = $this['coordinates'] ? $this['coordinates']['ne']['x'] : null;
        $maxY = $this['coordinates'] ? $this['coordinates']['ne']['y'] : null;
        return [
            'id' => $this['id'],
            'minCondenser' => $minCondenser,
            'maxCondenser' => $maxCondenser,
            'defaultCondenser' => $defaultCondenser,
            'minFocus' => $minFocus,
            'maxFocus' => $maxFocus,
            'magnification' => $this['testType']['magnification'] ?? 2,
            'magnificationPosition' => $magnificationPosition,
            'numLayer' => $numLayer,
            'numMergeLayer' => $numMergeLayer,
            'mergeAlgorithm' => $mergeAlgorithm,
            'stitchAlgorithm' => $stitchAlgorithm,
            'xStep' => $xStep,
            'yStep' => $yStep,
            'zStep ' => $zStep,
            'minX' => $minX,
            'minY' => $minY,
            'maxX' => $maxX,
            'maxY' => $maxY,
        ];
    }

    private function formatScanResponse(Scan $scan, bool $isRegion = false): array
    {
        // Initialize default values to avoid undefined variable issues
        $settings = [];
        $coordinates = [];
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
            }
        } else {
            // For a full scan, fetch settings and decode coordinates directly from the scan
            $settings = SettingsCategory::query()->where('magnification_and_condenser', 1)->get();
            $coordinates = JsonHelper::decodeJson($scan['slide_coordinates']);
        }

        // Return a structured response
        return [
            'id' => $id,
            'coordinates' => $coordinates,
            'settings' => $settings
        ];
    }

}
