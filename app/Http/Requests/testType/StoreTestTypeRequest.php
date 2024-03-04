<?php

namespace App\Http\Requests\testType;

use App\Rules\Odd;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreTestTypeRequest extends FormRequest
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
            'title' => ['required', 'string'],
            'code' => ['string'],
            'gender' => ['required', 'string', 'in:male,female,both'],
            'type' => ['required', 'string', 'in:invert,fluorescent,optical'],
            'numberOfLayers' => ['nullable', 'integer', 'min:1', new Odd()],
            'microStep' => ['nullable', 'integer'],
            'step' => ['nullable', 'integer',],
            'z' => ['nullable', 'integer'],
            'condenser' => ['nullable', 'integer'],
            'brightness' => ['nullable', 'integer', 'between:0,100'],
            'magnification' => ['required', 'integer'],
            'description' => ['nullable', 'string'],
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator
     * @return void
     */
//    public function withValidator(Validator $validator): void
//    {
//        $validator->sometimes(['microStep', 'step'], 'required|integer', function ($input) {
//            return $input->numberOfLayers > 1;
//        });
//    }
}
