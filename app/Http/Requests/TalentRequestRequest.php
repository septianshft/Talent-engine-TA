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
     * Prepare the data for validation.
     */
    public function prepareForValidation()
    {
        $this->merge([
            'details' => strip_tags($this->details),
            'work_location_country' => $this->work_location_country ? trim($this->work_location_country) : null,
            'work_location_city' => $this->work_location_city ? trim($this->work_location_city) : null,
        ]);
    }

    /**
     * Handle a passed validation attempt.
     */
    protected function passedValidation()
    {
        // Additional sanitization after validation passes
        $this->replace([
            'details' => htmlspecialchars($this->details, ENT_QUOTES, 'UTF-8'),
        ]);
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateCompetencies($validator);
            $this->validateWeightDistribution($validator);
            $this->validateLocationRequirements($validator);
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
        foreach ($competencies as $index => $competency) {
            if (!isset($competency['id']) || !isset($competency['level']) || !isset($competency['weight'])) {
                $validator->errors()->add(
                    "competencies.{$index}.fields",
                    'Each competency must have an ID, level, and weight specified.'
                );
            } elseif ($competency['weight'] === '' || $competency['weight'] === null) {
                $validator->errors()->add(
                    "competencies.{$index}.weight",
                    'Weight cannot be empty, please provide a value between 0 and 100.'
                );
            }
        }
    }

    /**
     * Validate weight distribution for thesis compliance
     * Updated to align with Enhanced DSS academic compliance logic
     */
    protected function validateWeightDistribution($validator)
    {
        $competencies = $this->input('competencies', []);

        if (empty($competencies)) {
            return;
        }

        $weights = array_column($competencies, 'weight');
        $totalWeight = array_sum($weights);

        // Academic compliance: Total weights should not exceed 100%
        if ($totalWeight > 100) {
            $validator->errors()->add('competencies',
                sprintf("Total weight (%.1f%%) exceeds 100%%. Please adjust weight distribution for academic compliance.", $totalWeight)
            );
            return; // Stop further validation if total exceeds 100%
        }

        // Academic scenario: Allow zero-weight competencies for location-only evaluation
        if ($totalWeight == 0) {
            // This is valid for location-only evaluation scenarios
            return;
        }

        // Academic compliance: Prevent extreme imbalance only when multiple non-zero competencies exist
        $nonZeroWeights = array_filter($weights, function($weight) { return $weight > 0; });
        if (count($nonZeroWeights) > 1 && $totalWeight > 0) {
            $maxWeight = max($weights);
            $dominanceRatio = $maxWeight / $totalWeight;

            // Allow up to 90% dominance, prevent only extreme cases (>90%)
            if ($dominanceRatio > 0.9) {
                $validator->errors()->add('competencies',
                    sprintf('Extreme weight imbalance detected (%.1f%% dominance). For academic rigor, consider more balanced distribution when using multiple competencies.',
                    $dominanceRatio * 100)
                );
            }
        }

        // Single competency at 100% is academically valid for specialized roles
        // No additional validation needed - this scenario is explicitly allowed
    }

    /**
     * Validate location requirements
     */
    protected function validateLocationRequirements($validator)
    {
        $workLocationType = $this->input('work_location_type');
        $country = $this->input('work_location_country');
        $city = $this->input('work_location_city');

        if (in_array($workLocationType, ['on_site', 'hybrid'])) {
            if (empty($country)) {
                $validator->errors()->add('work_location_country',
                    'Country is required for on-site or hybrid work arrangements.');
            }

            if (empty($city)) {
                $validator->errors()->add('work_location_city',
                    'City is required for on-site or hybrid work arrangements.');
            }
        }
    }
}
