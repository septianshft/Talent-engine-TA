<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CompetencyTalentRequest extends Pivot
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    // Define the table if it's different from the conventional name
    // protected $table = 'competency_talent_request';

    // Make pivot attributes fillable if you plan to create/update them directly using the pivot model instance
    // For attach/sync operations with withPivot, this is not strictly necessary for those attributes.
    // protected $fillable = [
    //     'competency_id',
    //     'talent_request_id',
    //     'required_proficiency_level',
    //     'weight',
    // ];
}
