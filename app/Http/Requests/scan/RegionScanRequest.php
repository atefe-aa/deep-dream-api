<?php

namespace App\Http\Requests\scan;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegionScanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user() && auth()->user()->hasRole(['superAdmin', 'operator']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'selectedRegions' => 'required|array',
            'selectedRegions.*.scanId' => 'required|integer',
            'selectedRegions.*.regions' => 'required|array',
            'selectedRegions.*.regions.*.sw.x' => 'required|numeric',
            'selectedRegions.*.regions.*.sw.y' => 'required|numeric',
            'selectedRegions.*.regions.*.ne.x' => 'required|numeric',
            'selectedRegions.*.regions.*.ne.y' => 'required|numeric',
        ];
    }
}
