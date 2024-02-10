<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonException;

class ScanRequestResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     * @throws JsonException
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
                        if ($category['title'] === '2x') {
                            $defaultCondenser = $setting['value'];
                        }
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

        $coordinates = json_decode($this['scan']['slide_coordinates'], true, 512, JSON_THROW_ON_ERROR);

        $minX = $coordinates ? $coordinates['sw']['x'] : null;
        $minY = $coordinates ? $coordinates['sw']['y'] : null;
        $maxX = $coordinates ? $coordinates['ne']['x'] : null;
        $maxY = $coordinates ? $coordinates['ne']['y'] : null;
        return [
            'id' => $this['scan']['id'],
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
            'minX' => $minX,
            'minY' => $minY,
            'maxX' => $maxX,
            'maxY' => $maxY,
        ];
    }
}
