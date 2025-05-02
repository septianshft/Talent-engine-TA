<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Database\Factories\TalentRequestFactory; // Import the factory

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
        'talent_id',
        'details',
        'status',
    ];

    /**
     * Get the user who created the request.
     */
    public function requestingUser(): BelongsTo // Renamed from user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the talent who is the target of the request.
     */
    public function assignedTalent(): BelongsTo // Renamed from talent()
    {
        return $this->belongsTo(User::class, 'talent_id');
    }

    /**
     * The competencies required for this request.
     */
    public function competencies(): BelongsToMany
    {
        return $this->belongsToMany(Competency::class, 'competency_talent_request')
                    ->withPivot('required_proficiency_level');
    }
}
