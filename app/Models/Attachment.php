<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'proof_id',
        'original_name',
        'path',
        'mime',
        'size',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (!$model->id) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function proof()
    {
        return $this->belongsTo(Proof::class);
    }

    protected $appends = ['url'];

public function getUrlAttribute()
{
    return Storage::url($this->path);
}
}
