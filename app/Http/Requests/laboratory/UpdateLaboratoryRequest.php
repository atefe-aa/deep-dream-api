<?php

namespace App\Http\Requests\laboratory;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLaboratoryRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "labName" => ['nullable','string'],
            "fullName" => ['nullable','string'],
            "phone" => ['nullable','string','unique:users'],
            "address" => ['nullable','string'],
            "description" => ['nullable','string'],
            "username" => ['nullable','string','unique:users'],
            "password" => ['nullable','string','confirmed'],
            "avatar" => ['nullable','image'],
            "signature" => ['nullable','image'],
            "header" => ['nullable','image'],
            "footer" => ['nullable','image'],
        ];
    }
}
