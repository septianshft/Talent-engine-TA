<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Competency extends Model
{
    use HasFactory;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'category',
        'description',
    ];

    /**
     * The users that possess this competency.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'competency_user');
    }

    /**
     * The talent requests that require this competency.
     */
    public function talentRequests(): BelongsToMany
    {
        return $this->belongsToMany(TalentRequest::class, 'competency_talent_request');
    }
}
