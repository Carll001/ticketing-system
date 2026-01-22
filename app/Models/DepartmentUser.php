<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Models\User;
use App\Models\Department;

class DepartmentUser extends Pivot
{
    use HasUuids;

    protected $table = 'department_users';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['user_id', 'department_id'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function department() {
        return $this->belongsTo(Department::class);
    }
}
