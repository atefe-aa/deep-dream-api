<?php

namespace App\Http\Requests\laboratory;

use Illuminate\Foundation\Http\FormRequest;

class StoreLaboratoryRequest extends FormRequest
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
           "avatar" => ['nullable','image'],
           "signature" => ['required','nullable','image'],
           "header" => ['required','nullable','image'],
           "footer" => ['nullable','image'],
           "labName" => ['required','string'],
           "fullName" => ['required','string'],
           "phone" => ['required','string','unique:users'],
           "address" => ['required','string'],
           "description" => ['nullable','string'],
           "username" => ['required','string','unique:users'],
           "password" => ['required','string','confirmed'],
        ];
    }
}
