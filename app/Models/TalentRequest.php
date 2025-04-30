<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TalentRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'talent_id',
        'details',
        'status',
    ];

    /**
     * Get the user who created the request.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the talent who is the target of the request.
     */
    public function talent()
    {
        return $this->belongsTo(User::class, 'talent_id');
    }
    //
}
