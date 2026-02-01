<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;
use App\Models\DepartmentUser;

class Department extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name'];

    /**
     * Get the users for this department
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'department_users',   // Pivot table
            'department_id',      // Foreign key on pivot table for this model
            'user_id'             // Foreign key on pivot table for related model
        )
            ->using(DepartmentUser::class)  // Link the custom pivot model
            ->withTimestamps();             // Manage created_at / updated_at automatically
    }
}
