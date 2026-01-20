<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot; // Change this
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class DepartmentUser extends Pivot // Extend Pivot
{
    use HasUuids;

    protected $table = 'department_users';

    // Since you have a UUID primary key on this pivot table
    public $incrementing = false;
    protected $keyType = 'string';

    // Allow mass assignment for when you use create() or sync()
    protected $fillable = ['user_id', 'department_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}