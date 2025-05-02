<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Competency extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * The users (talents) that possess this competency.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'competency_user');
    }

    /**
     * The talent requests that require this competency.
     */
    public function talentRequests(): BelongsToMany
    {
        return $this->belongsToMany(TalentRequest::class, 'competency_talent_request')
                    ->withPivot('required_proficiency_level');
    }
}
