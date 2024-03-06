<?php

namespace App\Http\Requests\laboratory;

use App\Models\Laboratory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        $labId = $this->route('laboratory');
        $laboratory = Laboratory::findOrFail($labId);
        $userId = $laboratory->user->id;

        $rules = [
            "labName" => ['nullable', 'string'],
            "fullName" => ['nullable', 'string'],
            "address" => ['nullable', 'string'],
            "description" => ['nullable', 'string'],
//            "username" => ['nullable', 'string', 'unique:users'], //username update is not available for now
            "password" => ['nullable', 'string', 'confirmed'],
            "avatar" => ['nullable', 'image'],
            "signature" => ['nullable', 'image'],
            "header" => ['nullable', 'image'],
            "footer" => ['nullable', 'image'],
        ];

        // Check if the user ID associated with the laboratory is valid
        if ($userId) {
            $rules['phone'] = ['nullable', 'string', Rule::unique('users', 'phone')->ignore($userId)];
        } else {
            // If for some reason the user ID is not available, perform a standard uniqueness check
            $rules['phone'] = ['nullable', 'string', 'unique:users,phone'];
        }

        return $rules;
    }
}
