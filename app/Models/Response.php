<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Response extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'step_field_id',
        'user_id',
        'response',
    ];

    /**
     * Get the field that this response belongs to.
     */
    public function field(): BelongsTo
    {
        // Assuming your field model is named 'Field'
        return $this->belongsTo(StepField::class, 'step_field_id');
    }

    /**
     * Get the user who submitted the response.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
