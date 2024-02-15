<?php

namespace App\Http\Requests\share;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ShareRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'testId' => ['required', 'exists:tests,id'],
            'counsellors' => ['required', 'array'],
            'counsellors.*' => ['required', 'exists:counsellors,id']
        ];
    }
}
