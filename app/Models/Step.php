<?php

namespace App\Models;

use App\Models\Proof;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Step extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        "task_id", "title","description","assigned_to", "status"
    ];

    public function task()
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    public function assigned()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function fields() {
        return $this->hasMany(StepField::class);
    }

    public function proofs()
{
    return $this->hasMany(Proof::class)->with('attachments', 'user');
}


}
