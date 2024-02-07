<?php

namespace App\Http\Requests\slide;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSlideRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $slideId = $this->route('slide'); // Assuming 'slide' is the route parameter name for the slide's ID

        return [
            'nth' => ['integer', 'unique:slides,nth,' . $slideId], // Exclude the current slide from the unique check
            'sw_x' => ['numeric', 'between:0,999.999'],
            'sw_y' => ['numeric', 'between:0,999.999'],
            'ne_x' => ['numeric', 'between:0,999.999'],
            'ne_y' => ['numeric', 'between:0,999.999'],
        ];
    }
}
