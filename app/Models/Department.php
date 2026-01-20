<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Department extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        "name"
    ];

    /**
     * Get the users for this department
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'department_users', 'department_id', 'user_id');
    }
}
