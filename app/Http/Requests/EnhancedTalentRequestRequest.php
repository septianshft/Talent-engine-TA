<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EnhancedTalentRequestRequest extends FormRequest
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
            'project_title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'location' => 'required|string|max:255',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'budget_min' => 'required|numeric|min:0',
            'budget_max' => 'required|numeric|gt:budget_min',
            'required_competencies' => 'required|array|min:1|max:10',
            'required_competencies.*.name' => 'required|string|max:100',
            'required_competencies.*.required_level' => 'required|integer|min:1|max:5',
            'required_competencies.*.weight' => 'required|numeric|min:0.1|max:100',
            'required_competencies.*.is_critical' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'required_competencies.required' => 'At least one competency must be specified.',
            'required_competencies.min' => 'At least one competency must be specified.',
            'required_competencies.max' => 'Maximum 10 competencies allowed.',
            'required_competencies.*.name.required' => 'Competency name is required.',
            'required_competencies.*.required_level.min' => 'Required level must be at least 1.',
            'required_competencies.*.required_level.max' => 'Required level cannot exceed 5.',
            'required_competencies.*.weight.min' => 'Weight must be at least 0.1.',
            'required_competencies.*.weight.max' => 'Weight cannot exceed 100.',
            'budget_max.gt' => 'Maximum budget must be greater than minimum budget.',
            'start_date.after_or_equal' => 'Start date cannot be in the past.',
            'end_date.after' => 'End date must be after start date.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateWeightDistribution($validator);
            $this->validateCriticalCompetencies($validator);
        });
    }

    /**
     * Validate weight distribution to prevent extreme assignments
     */
    protected function validateWeightDistribution($validator)
    {
        $competencies = $this->input('required_competencies', []);

        if (empty($competencies)) {
            return;
        }

        $weights = array_column($competencies, 'weight');
        $totalWeight = array_sum($weights);

        if ($totalWeight <= 0) {
            $validator->errors()->add('required_competencies', 'Total weight must be positive.');
            return;
        }

        // Check maximum single weight percentage (60%)
        $maxWeight = max($weights);
        $maxPercentage = $maxWeight / $totalWeight;

        if ($maxPercentage > 0.6) {
            $validator->errors()->add(
                'required_competencies',
                'Single competency weight cannot exceed 60% of total weight. Current maximum: ' .
                round($maxPercentage * 100, 1) . '%'
            );
        }

        // Check weight distribution variance
        $variance = $this->calculateVariance($weights, $totalWeight);
        if ($variance > 0.8) {
            $validator->errors()->add(
                'required_competencies',
                'Weight distribution is too uneven. Consider more balanced weight allocation.'
            );
        }
    }

    /**
     * Validate critical competency specifications
     */
    protected function validateCriticalCompetencies($validator)
    {
        $competencies = $this->input('required_competencies', []);
        $criticalCount = 0;

        foreach ($competencies as $index => $competency) {
            if (!empty($competency['is_critical'])) {
                $criticalCount++;

                // Critical competencies should have reasonable required levels
                if (isset($competency['required_level']) && $competency['required_level'] < 3) {
                    $validator->errors()->add(
                        "required_competencies.{$index}.required_level",
                        'Critical competencies should require at least level 3 proficiency.'
                    );
                }
            }
        }

        // Limit number of critical competencies
        if ($criticalCount > count($competencies) * 0.5) {
            $validator->errors()->add(
                'required_competencies',
                'Too many critical competencies. Maximum 50% of competencies should be marked as critical.'
            );
        }
    }

    /**
     * Calculate variance for weight distribution validation
     */
    private function calculateVariance(array $weights, float $totalWeight): float
    {
        $count = count($weights);
        if ($count <= 1) return 0;

        // Normalize weights
        $normalizedWeights = array_map(function($weight) use ($totalWeight) {
            return $weight / $totalWeight;
        }, $weights);

        $mean = array_sum($normalizedWeights) / $count;
        $sumSquaredDifferences = array_sum(array_map(function($weight) use ($mean) {
            return pow($weight - $mean, 2);
        }, $normalizedWeights));

        return $sumSquaredDifferences / $count;
    }

    /**
     * Get the error messages for the defined validation rules with context
     */
    public function getValidationErrorsWithContext(): array
    {
        $errors = [];

        if ($this->validator && $this->validator->fails()) {
            foreach ($this->validator->errors()->messages() as $field => $messages) {
                $errors[$field] = [
                    'messages' => $messages,
                    'suggestions' => $this->getFieldSuggestions($field)
                ];
            }
        }

        return $errors;
    }

    /**
     * Get improvement suggestions for specific fields
     */
    private function getFieldSuggestions(string $field): array
    {
        $suggestions = [
            'required_competencies' => [
                'Ensure weights are distributed evenly across competencies',
                'Limit critical competencies to truly essential skills',
                'Use weights between 10-50 for balanced evaluation',
                'Consider the relative importance of each competency'
            ],
            'budget_min' => [
                'Set realistic minimum budget based on market rates',
                'Consider project complexity and duration'
            ],
            'budget_max' => [
                'Ensure maximum budget allows for quality talent',
                'Leave room for negotiation within the range'
            ],
            'start_date' => [
                'Allow adequate time for talent selection',
                'Consider talent availability and notice periods'
            ],
            'end_date' => [
                'Provide realistic timeline for project completion',
                'Account for potential delays and revisions'
            ]
        ];

        // Check for specific weight-related issues
        if (strpos($field, 'weight') !== false || $field === 'required_competencies') {
            return $suggestions['required_competencies'];
        }

        return $suggestions[$field] ?? ['Review the field requirements and try again'];
    }
}
