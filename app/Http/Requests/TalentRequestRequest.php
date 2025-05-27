<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TalentRequestRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'details' => 'required|string|max:1000',
            'work_location_type' => 'required|string|in:remote,on_site,hybrid',
            'work_location_country' => 'nullable|string|max:255|required_if:work_location_type,on_site,hybrid',
            'work_location_city' => 'nullable|string|max:255|required_if:work_location_type,on_site,hybrid',
            'competencies' => 'required|array|min:1',
            'competencies.*.id' => 'required|integer|exists:competencies,id',
            'competencies.*.level' => 'required|integer|between:1,4', // Assuming proficiency level is 1-4
            'competencies.*.weight' => 'required|integer|min:0|max:100', // Updated weight validation
            'talent_id' => 'nullable|integer|exists:users,id', // For direct requests
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'details.required' => 'Please provide details about your request.',
            'details.max' => 'Details cannot exceed 1000 characters.',

            'work_location_type.required' => 'Please select a work location type.',
            'work_location_type.in' => 'Invalid work location type selected.',
            'work_location_country.required_if' => 'Country is required for on-site or hybrid work locations.',
            'work_location_city.required_if' => 'City is required for on-site or hybrid work locations.',

            'competencies.required' => 'Please select at least one competency.',
            'competencies.min' => 'Please select at least one competency.',
            'competencies.*.id.required' => 'Competency ID is required.',
            'competencies.*.id.exists' => 'Selected competency does not exist.',
            'competencies.*.level.required' => 'Please select a proficiency level for all competencies.',
            'competencies.*.level.between' => 'Proficiency level must be between 1 and 4.',
            'competencies.*.weight.required' => 'Please select a weight for all competencies.',
            'competencies.*.weight.min' => 'Weight must be at least 0%.',
            'competencies.*.weight.max' => 'Weight must not exceed 100%.',
            'competencies.*.weight.integer' => 'Weight must be a whole number (0-100).',

            'talent_id.exists' => 'Selected talent does not exist.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateCompetencies($validator);
        });
    }

    /**
     * Validate competency requirements
     */
    protected function validateCompetencies($validator)
    {
        $competencies = $this->input('competencies', []);

        if (empty($competencies)) {
            return;
        }

        // Check for duplicate competencies
        $competencyIds = array_column($competencies, 'id');
        if (count($competencyIds) !== count(array_unique($competencyIds))) {
            $validator->errors()->add('competencies', 'Duplicate competencies are not allowed.');
        }

        // Validate that all competencies have required fields
        // Note: 'weight' can now be 0, so !empty($competency['weight']) might be problematic if 0 is submitted and treated as empty.
        // The 'required' and 'integer' rules for competencies.*.weight should handle presence and type.
        // We need to ensure that '0' is not considered 'empty' in a way that bypasses other checks or causes issues here.
        // PHP's empty() treats '0' (string) and 0 (int) as empty.
        // It's better to check for null or if the key is not set if 0 is a valid value.

        foreach ($competencies as $index => $competency) {
            if (!isset($competency['id']) || !isset($competency['level']) || !isset($competency['weight'])) {
                $validator->errors()->add(
                    "competencies.{$index}.fields", // More specific error key
                    'Each competency must have an ID, level, and weight specified.'
                );
            } elseif ($competency['weight'] === '' || $competency['weight'] === null) {
                 // This case handles if weight is submitted as an empty string, which wouldn't be caught by min:0 if not numeric
                 $validator->errors()->add(
                    "competencies.{$index}.weight",
                    'Weight cannot be empty, please provide a value between 0 and 100.'
                );
            }
        }
    }
}
