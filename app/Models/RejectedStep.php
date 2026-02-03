<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RejectedStep extends Model
{
    use HasUuids;

    protected $fillable = [
        'step_id',
        'rejected_by',
        'reason',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function step()
    {
        return $this->belongsTo(Step::class);
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }
}