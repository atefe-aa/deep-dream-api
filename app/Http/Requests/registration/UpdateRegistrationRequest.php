<?php

namespace App\Http\Requests\registration;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "name" => ['nullable', 'string'],
            "nationalId" => ['nullable'],
            "age" => ['nullable', 'integer'],
            "doctorName" => ['nullable', 'string'],
            "ageUnit" => ['nullable', 'string', 'in:year,day'],
            "gender" => ['nullable', 'string', 'in:male,female'],
            "testType" => ['nullable', 'exists:test_types,id'],
            "laboratoryId" => ['nullable', 'exists:laboratories,id'],
            "description" => ['nullable', 'string'],
            "senderRegisterCode" => ['nullable', 'string'],
            "numberOfSlides" => ['integer'],
        ];
    }
}
