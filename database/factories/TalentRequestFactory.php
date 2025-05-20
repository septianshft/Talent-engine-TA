<?php

namespace Database\Factories;

use App\Models\TalentRequest;
use App\Models\User;
use App\Models\Competency;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Log;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TalentRequest>
 */
class TalentRequestFactory extends Factory
{
    protected $model = TalentRequest::class;

    // Flag to control default competency attachment
    protected bool $shouldSkipDefaultCompetencies = false;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        Log::debug('[TalentRequestFactory] Definition method called.');
        return [
            // Simplified to avoid Role issue for now. User should verify their User factory setup.
            'user_id' => User::factory(),
            'details' => $this->faker->paragraph,
            'status' => $this->faker->randomElement(['pending_admin', 'pending_review', 'in_progress', 'completed', 'cancelled']),
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure()
    {
        return $this->afterCreating(function (TalentRequest $talentRequest) {
            Log::info('[TalentRequestFactory] afterCreating called.', ['talent_request_id' => $talentRequest->id, 'shouldSkip' => $this->shouldSkipDefaultCompetencies]);

            if (!$this->shouldSkipDefaultCompetencies) {
                Log::info('[TalentRequestFactory] Attaching default competencies.', ['talent_request_id' => $talentRequest->id]);
                $defaultCompetencies = Competency::whereIn('name', ['Communication', 'Teamwork', 'Problem Solving'])->get();

                if ($defaultCompetencies->isNotEmpty()) {
                    $attachData = [];
                    foreach ($defaultCompetencies as $competency) {
                        $attachData[$competency->id] = [
                            'required_proficiency_level' => 3, // Default level
                            'weight' => 4, // Default weight
                        ];
                    }
                    $talentRequest->competencies()->attach($attachData);
                    Log::info('[TalentRequestFactory] Default competencies attached.', ['talent_request_id' => $talentRequest->id, 'attached_data' => $attachData]);
                } else {
                    Log::info('[TalentRequestFactory] No default competencies found to attach.');
                }
            } else {
                Log::info('[TalentRequestFactory] Skipping default competencies attachment.', ['talent_request_id' => $talentRequest->id]);
            }

            // Reset the flag *after* its use, for the next factory operation within the same test or process
            Log::debug('[TalentRequestFactory] Resetting shouldSkipDefaultCompetencies flag to false.', ['talent_request_id' => $talentRequest->id]);
            $this->shouldSkipDefaultCompetencies = false;
        });
    }

    /**
     * Indicate that default competencies should not be attached for this creation call.
     */
    public function skipDefaultCompetencies()
    {
        Log::info('[TalentRequestFactory] skipDefaultCompetencies() called. Setting shouldSkipDefaultCompetencies to true.');
        $this->shouldSkipDefaultCompetencies = true;

        // This state call is important for the factory's internal mechanics,
        // even if we're primarily using a class property.
        return $this->state(function (array $attributes) {
            Log::debug('[TalentRequestFactory] Applying state for skipDefaultCompetencies.');
            return $attributes; // No actual attribute change needed from state for this specific flag
        });
    }

    /**
     * Indicate that the talent request should have specific assigned talents.
     * This method seems to be from the original file, keeping it.
     * @param  int  $count
     * @param  string  $pivotStatus
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function withAssignedTalents(int $count = 1, string $pivotStatus = 'pending_assignment_response')
    {
        // Note: Chaining afterCreating like this will add multiple afterCreating callbacks.
        // It's generally better to have one configure() method that chains all afterCreating logic
        // or to ensure the order of operations is as expected if multiple are used.
        // For this specific case, it should be fine as it's a separate concern from default competencies.
        return $this->afterCreating(function (TalentRequest $talentRequest) use ($count, $pivotStatus) {
            $talents = User::where('role', 'talent')->inRandomOrder()->take($count)->get();

            if ($talents->count() < $count) {
                Log::warning("[TalentRequestFactory] Not enough talents to assign. Requested: {$count}, Found: {$talents->count()}");
            }

            foreach ($talents as $talent) {
                $talentRequest->assignedTalents()->attach($talent->id, ['status' => $pivotStatus]);
            }
            Log::info("[TalentRequestFactory] Assigned {$talents->count()} talents to TalentRequest ID {$talentRequest->id} with status '{$pivotStatus}'.");
        });
    }
}
