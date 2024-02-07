<?php

namespace App\Http\Requests\slide;

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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nth' => ['required', 'integer', 'unique:slides,nth'],
            'sw_x' => ['required', 'decimal:0,3'],
            'sw_y' => ['required', 'decimal:0,3'],
            'ne_x' => ['required', 'decimal:0,3'],
            'ne_y' => ['required', 'decimal:0,3'],
        ];
    }
}
