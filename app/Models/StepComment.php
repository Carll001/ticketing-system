<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Step;


class StepComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'step_id',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function step()
    {
        return $this->belongsTo(Step::class);
    }
}
