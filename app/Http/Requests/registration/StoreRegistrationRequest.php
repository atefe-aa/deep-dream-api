<?php

namespace App\Http\Requests\registration;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => ['required','string'],
            "nationalId" => ['required'],
            "age" => ['required','integer'],
            "doctorName" => ['required','string'],
            "ageUnit" => ['required','string', 'in:year,day'],
            "gender" => ['required','string', 'in:male,female'],
            "testType" => ['required','exists:test_types,id'],
            "laboratoryId" => ['required','exists:laboratories,id'],
            "description" => ['nullable','string'],
            "senderRegisterCode" => ['string'],
            "numberOfSlides" => ['integer'],
        ];
    }
}
