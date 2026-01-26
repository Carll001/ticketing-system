<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'content',
        'transaction_number',
        'user_id',
        'task_id',
        'step_id',
        'department_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    public function step()
    {
        return $this->belongsTo(Step::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    protected static function boot()
    {
        parent::boot(); // Always call parent::boot() in the boot method

        static::creating(function ($transaction) {
            $lastTransaction = static::orderBy('id', 'desc')->first();

            // If there are NO transactions yet
            if (!$lastTransaction) {
                $transaction->transaction_number = 'TSK0001';
            } else {
                // Get the numeric part of the last number
                $number = preg_replace('/[^0-9]/', '', $lastTransaction->transaction_number);

                // Increment and pad (e.g., TSK0002)
                $transaction->transaction_number = 'TSK' . str_pad((int) $number + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }
}
