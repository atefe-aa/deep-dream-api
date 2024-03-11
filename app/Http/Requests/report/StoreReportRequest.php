<?php

namespace App\Http\Requests\report;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReportRequest extends FormRequest
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
            'id' => 'required|integer|exists:report_templates,id',
            'testId' => 'required|integer|exists:tests,id',
            'testTitle' => 'required|string',
            'sections' => 'required|array',
            'sections.*.id' => 'required|integer',
            'sections.*.sectionTitle' => 'required|string',
            'sections.*.groups' => 'required|array',
            'sections.*.groups.*.title' => 'nullable|string',
            'sections.*.groups.*.className' => 'nullable|string',
            'sections.*.groups.*.options' => 'required|array',
            'sections.*.groups.*.options.*.id' => 'required|integer',
            'sections.*.groups.*.options.*.type' => 'required|string',
            'sections.*.groups.*.options.*.value' => 'required',
            'sections.*.groups.*.options.*.label' => 'nullable|string',
            'sections.*.groups.*.options.*.className' => 'nullable|string',
        ];
    }
}
