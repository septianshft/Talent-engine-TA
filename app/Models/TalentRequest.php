<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Database\Factories\TalentRequestFactory; // Import the factory
use App\Models\CompetencyTalentRequest; // Import the custom pivot model

class TalentRequest extends Model
{
    use HasFactory;

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return TalentRequestFactory::new();
    }

    protected $fillable = [
        'user_id',
        // 'talent_id', // Removed as it's now a many-to-many relationship
        'details',
        'status',
    ];

    /**
     * Get the user who created the request.
     */
    public function requestingUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * The talents assigned to this request.
     */
    public function assignedTalents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'talent_request_assignments', 'talent_request_id', 'talent_id')
                    ->withPivot('status') // To get the status of each assignment
                    ->withTimestamps(); // If you want to track when assignments are created/updated
    }

    /**
     * The competencies required for this request.
     */
    public function competencies(): BelongsToMany
    {
        return $this->belongsToMany(Competency::class, 'competency_talent_request')
                    ->using(CompetencyTalentRequest::class) // Use the custom pivot model
                    ->withPivot('required_proficiency_level', 'weight');
    }
}
