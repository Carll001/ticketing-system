<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class PresetField extends Model
{
    use HasUuids;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['preset_id', 'type', 'label'];

    public function preset()
    {
        return $this->belongsTo(Preset::class);
    }
}
