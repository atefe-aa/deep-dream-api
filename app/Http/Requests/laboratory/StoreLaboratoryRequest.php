<?php

namespace App\Http\Requests\laboratory;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreLaboratoryRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            "avatar" => ['nullable', 'image'],
            "signature" => ['required', 'image'],
            "header" => ['required', 'image'],
            "footer" => ['nullable', 'image'],
            "labName" => ['required', 'string'],
            "fullName" => ['required', 'string'],
            "phone" => ['required', 'string', 'unique:users'],
            "address" => ['required', 'string'],
            "description" => ['nullable', 'string'],
            "username" => ['required', 'string', 'unique:users'],
            "password" => ['required', 'string', 'confirmed'],
        ];
    }
}
