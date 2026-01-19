<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StepField extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'step_id',
        'type',
        'label',
        'required', // Highly recommended to include this
    ];

    /**
     * Get the step that owns the field.
     */
    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }
}