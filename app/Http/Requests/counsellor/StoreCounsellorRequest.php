<?php

namespace App\Http\Requests\counsellor;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCounsellorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user() && auth()->user()->hasRole(['superAdmin', 'laboratory']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
//            if the creator is a super admin then it needs to pass along the lab id
        $rules = [
            'name' => ['required', 'string'],
            'phone' => ['required', 'string'],
            'description' => ['nullable', 'string'],
        ];

        // Check if the user is a super admin or operator
        if (auth()->user()->hasRole(['superAdmin'])) {
            // If they are, require 'labId'
            $rules['laboratoryId'] = ['required', 'exists:laboratories,id'];
        } else {
            // If not, 'laboratoryId' can be nullable
            $rules['laboratoryId'] = ['nullable', 'exists:laboratories,id'];
        }

        return $rules;
    }
}
