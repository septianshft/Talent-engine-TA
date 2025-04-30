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
    //
}
