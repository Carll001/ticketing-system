<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Proof extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'step_id',
        'user_id',
        'description',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    // ==========================
    // Relationships
    // ==========================

    public function attachments()
    {
        return $this->hasMany(Attachment::class);
    }

    public function step()
    {
        return $this->belongsTo(Step::class, 'step_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
