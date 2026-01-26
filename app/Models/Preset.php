<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Preset extends Model
{
    use HasUuids; // Important for UUIDs

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['name', 'description', 'has_cost', 'user_id'];

    public function fields()
    {
        return $this->hasMany(PresetField::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
