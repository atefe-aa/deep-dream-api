<?php

namespace App\Http\Requests\testType;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTEstTypeRequest extends FormRequest
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
     * @return array
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string'],
            'template' => ['nullable', 'exists:report_templates,id'],
            'code' => ['nullable', 'string'],
            'gender' => ['nullable', 'string', 'in:male,female,both'],
            'type' => ['nullable', 'string', 'in:invert,fluorescent,optical'],
            'numberOfLayers' => ['nullable', 'integer', 'min:1'],
            'microStep' => ['nullable', 'integer'],
            'step' => ['nullable', 'integer',],
            'z' => ['nullable', 'integer'],
            'condenser' => ['nullable', 'integer'],
            'brightness' => ['nullable', 'integer'],
            'magnification' => ['nullable', 'integer'],
            'description' => ['nullable', 'string'],
        ];
    }
}
