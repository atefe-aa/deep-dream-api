<?php

namespace App\Http\Requests\counsellor;

use App\Rules\IranPhoneNumber;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCounsellorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;

    }

    /**
     * @return array[]
     */
    public function rules(): array
    {

        return [
            'name' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', new IranPhoneNumber()],
            'description' => ['nullable', 'nullable', 'string'],
            'laboratoryId' => ['nullable', 'exists:laboratories,id']
        ];
    }
}
