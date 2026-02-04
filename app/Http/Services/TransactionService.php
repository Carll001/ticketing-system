<?php

namespace App\Http\Services;

use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;

class TransactionService
{
    /**
     * Log a transaction for user creation
     */
    public function logUserCreated($user)
    {
        $content = Auth::user()->name . ' created a user named: ' . $user->name;

        return $this->createTransaction($content);
    }

    /**
     * Log a transaction for user update
     */
    public function logUserUpdated($user, $changes = [])
    {
        $changeDescription = !empty($changes) ? ' - Changes: ' . implode(', ', array_keys($changes)) : '';
        $content = Auth::user()->name . ' updated a user named: ' . $user->name;

        return $this->createTransaction($content);
    }

    /**
     * Log a transaction for user deletion
     */
    public function logUserDeleted($userName)
    {
        $content = Auth::user()->name . ' deleted a user named: ' . $userName;
        return $this->createTransaction($content);
    }

    /**
     * Log a transaction for department creation
     */
    public function logDepartmentCreated($department)
    {

        $content = Auth::user()->name . ' created a department named: ' . $department->name;
        return $this->createTransaction($content);
    }

    /**
     * Log a transaction for department update
     */
    public function logDepartmentUpdated($department, $changes = [])
    {
        $changeDescription = !empty($changes) ? ' - Changes: ' . implode(', ', array_keys($changes)) : '';
        $content = Auth::user()->name . 'Step ' . $department->name . ' updated  ' . $changeDescription;

        return $this->createTransaction($content);
    }

    /**
     * Log a transaction for department deletion
     */
    public function logDepartmentDeleted($departmentName, $departmentId)
    {
        $content = Auth::user()->name . ' deleted a department named ' . $departmentName;

        return $this->createTransaction($content);
    }

    /**
     * Log a transaction for step creation
     */
    public function logStepCreated($step)
    {
        $content = Auth::user()->name . ' created a Step named: ' . $step->title;
        return $this->createTransaction($content);
    }

    /**
     * Log a transaction for step update
     */
    public function logStepUpdated($step, $changes = [])
    {
        $changeDescription = !empty($changes) ? ' - Changes: ' . implode(', ', array_keys($changes)) : '';
        $content = Auth::user()->name . 'Step ' . $step->title . ' status changed to ' . $changeDescription;

        return $this->createTransaction($content);
    }

    /**
     * Log a transaction for step deletion
     */
    public function logStepDeleted($stepTitle)
    {
        $content = Auth::user()->name . 'Step ' . $stepTitle . ' deleted';
        return $this->createTransaction($content);
    }

    /**
     * Log a transaction for step status change
     */
    public function logStepStatusChanged($step, $newStatus)
    {
        $content = Auth::user()->name . 'Step ' . $step->title . ' status changed to ' . $newStatus;

        return $this->createTransaction($content);
    }

    /**
     * Generic method to create a transaction record
     */
    private function createTransaction($content)
    {
        return Transaction::create([
            'content' => $content,
        ]);
    }
}
