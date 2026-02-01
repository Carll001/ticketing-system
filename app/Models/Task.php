<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        "title",
        "description",
        "creator_id",
        "assigned_to",
        "type",
        'due_date',
        'order',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function creator()
    {
        return $this->BelongsTo(User::class, 'creator_id');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(Step::class);
    }

    public function assigned()
    {
        return $this->belongsTo(Department::class, 'assigned_to');
    }

    public function getNextAvailableStep($userId = null)
    {
        if ($this->order === 'random') {
            // For random order, return any incomplete step
            return $this->steps()
                ->where('status', '!=', 'completed')
                ->when($userId, fn($q) => $q->where('assigned_to', $userId))
                ->first();
        }

        // For sequential order
        return $this->steps()
            ->where('status', '!=', 'completed')
            ->when($userId, fn($q) => $q->where('assigned_to', $userId))
            ->orderBy('position')
            ->first();
    }

    public function canAccessStep($stepId, $userId = null)
    {
        $step = $this->steps()->find($stepId);

        if (!$step) return false;

        // If random order, all steps are accessible
        if ($this->order === 'random') {
            return true;
        }

        // For sequential order, check if all previous steps are completed
        $previousSteps = $this->steps()
            ->where('position', '<', $step->position)
            ->get();

        return $previousSteps->every(fn($s) => $s->status,['accepted','completed']);
    }
}
