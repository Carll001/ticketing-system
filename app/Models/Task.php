<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    
    protected $fillable = [
        "title","description","creator_id", "assigned_to", "type", 'due_date'
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    public function creator()
    {
        return $this->BelongsTo(User::class, 'creator_id');
    }
}
