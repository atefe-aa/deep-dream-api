<?php

namespace App\Http\Requests\slide;

use App\Rules\UniqueCoordinates;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSlideRequest extends FormRequest
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
            'nth' => ['required', 'integer', 'unique:slides,nth'],
            'coordinates.sw_x' => ['numeric', 'between:0,999.999'],
            'coordinates.sw_y' => ['numeric', 'between:0,999.999'],
            'coordinates.ne_x' => ['numeric', 'between:0,999.999'],
            'coordinates.ne_y' => ['numeric', 'between:0,999.999'],
            'coordinates' => ['required', new UniqueCoordinates()],
        ];
    }
}
