<?php

namespace App\Http\Requests\price;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Log;

class StorePriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user() && auth()->user()->hasRole('superAdmin');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'labId' => 'required|exists:laboratories,id',
            'testType' => [
                'required',
                'exists:test_types,id',
                Rule::unique('prices', 'test_type_id')->where(function ($query) {
                    return $query->where('lab_id', $this->input('labId'))
                        ->where('test_type_id', $this->input('testType'));
                }),
            ],
            "price" => ['required', 'integer'],
            "description" => ['nullable', 'string'],
            "extraPrice" => ['required', 'integer'],
        ];
    }
}
