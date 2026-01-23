<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StepField extends Model
{
    use HasUuids;

    // UUID primary key
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'step_id',    // links to the step
        'type',       // field type: text, file, checkbox, etc.
        'label',      // display label
        'required',   // true/false if required
    ];

    /**
     * Get the step that owns the field.
     */
    public function step(): BelongsTo
    {
        return $this->belongsTo(Step::class);
    }

    /**
     * Get the responses associated with this field.
     */
    public function responses(): HasMany
    {
        return $this->hasMany(Response::class, 'step_field_id');
    }
}
