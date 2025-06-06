<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TalentSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'filters',
        'description',
        'is_default'
    ];

    protected $casts = [
        'filters' => 'array',
        'is_default' => 'boolean'
    ];

    /**
     * Get the user that owns the search
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the search URL with filters applied
     */
    public function getSearchUrlAttribute(): string
    {
        $filters = $this->filters ?? [];
        return route('user.talents.index', $filters);
    }
}
