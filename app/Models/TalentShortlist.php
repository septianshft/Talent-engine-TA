<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TalentShortlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'talent_id',
        'list_name',
        'notes',
        'priority'
    ];

    protected $casts = [
        'priority' => 'integer'
    ];

    /**
     * Get the user that owns the shortlist
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the talent in the shortlist
     */
    public function talent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'talent_id');
    }

    /**
     * Get priority label
     */
    public function getPriorityLabelAttribute(): string
    {
        return match($this->priority) {
            1 => 'High',
            2 => 'Urgent',
            default => 'Normal'
        };
    }

    /**
     * Get priority color class
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            1 => 'text-yellow-600 bg-yellow-100',
            2 => 'text-red-600 bg-red-100',
            default => 'text-gray-600 bg-gray-100'
        };
    }
}
