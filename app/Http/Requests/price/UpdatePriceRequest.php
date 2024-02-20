<?php

namespace App\Http\Requests\price;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user() && auth()->user()->hasRole('superAdmin');
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            "price" => ['nullable', 'integer'],
            "description" => ['nullable', 'string'],
            "extraPrice" => ['nullable', 'integer'],
        ];
    }
}
