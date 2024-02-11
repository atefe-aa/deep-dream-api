<?php

namespace App\Http\Resources;

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
                        $defaultCondenser = isset($this['testType']) && $this['testType']['condenser']
                            ? $this['testType']['condenser']
                            : $setting['value'];
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
                        $zStep = isset($this['testType']) && $this['testType']['z_axis']
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
}
