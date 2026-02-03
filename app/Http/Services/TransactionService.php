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
        return $this->createTransaction(
            "User '{$user->name}' ({$user->email}) created",
            'USER_CREATED',
            Auth::id()
        );
    }

    /**
     * Log a transaction for user update
     */
    public function logUserUpdated($user, $changes = [])
    {
        $changeDescription = !empty($changes) ? ' - Changes: ' . implode(', ', array_keys($changes)) : '';
        
        return $this->createTransaction(
            "User '{$user->name}' ({$user->email}) updated{$changeDescription}",
            'USER_UPDATED',
            Auth::id()
        );
    }

    /**
     * Log a transaction for user deletion
     */
    public function logUserDeleted($userName, $userEmail)
    {
        return $this->createTransaction(
            "User '{$userName}' ({$userEmail}) deleted",
            'USER_DELETED',
            Auth::id()
        );
    }

    /**
     * Log a transaction for department creation
     */
    public function logDepartmentCreated($department)
    {
        return $this->createTransaction(
            "Department '{$department->name}' created",
            'DEPARTMENT_CREATED',
            Auth::id(),
            null,
            null,
            $department->id
        );
    }

    /**
     * Log a transaction for department update
     */
    public function logDepartmentUpdated($department, $changes = [])
    {
        $changeDescription = !empty($changes) ? ' - Changes: ' . implode(', ', array_keys($changes)) : '';
        
        return $this->createTransaction(
            "Department '{$department->name}' updated{$changeDescription}",
            'DEPARTMENT_UPDATED',
            Auth::id(),
            null,
            null,
            $department->id
        );
    }

    /**
     * Log a transaction for department deletion
     */
    public function logDepartmentDeleted($departmentName, $departmentId)
    {
        return $this->createTransaction(
            "Department '{$departmentName}' deleted",
            'DEPARTMENT_DELETED',
            Auth::id(),
            null,
            null,
            $departmentId
        );
    }

    /**
     * Log a transaction for step creation
     */
    public function logStepCreated($step)
    {
        return $this->createTransaction(
            "Step '{$step->title}' created",
            'STEP_CREATED',
            Auth::id(),
            $step->task_id,
            $step->id
        );
    }

    /**
     * Log a transaction for step update
     */
    public function logStepUpdated($step, $changes = [])
    {
        $changeDescription = !empty($changes) ? ' - Changes: ' . implode(', ', array_keys($changes)) : '';
        
        return $this->createTransaction(
            "Step '{$step->title}' updated{$changeDescription}",
            'STEP_UPDATED',
            Auth::id(),
            $step->task_id,
            $step->id
        );
    }

    /**
     * Log a transaction for step deletion
     */
    public function logStepDeleted($stepTitle, $taskId, $stepId)
    {
        return $this->createTransaction(
            "Step '{$stepTitle}' deleted",
            'STEP_DELETED',
            Auth::id(),
            $taskId,
            $stepId
        );
    }

    /**
     * Log a transaction for step status change
     */
    public function logStepStatusChanged($step, $newStatus)
    {
        $statusLabel = str_replace('-', ' ', ucfirst($newStatus));
        
        return $this->createTransaction(
            "Step '{$step->title}' status changed to {$statusLabel}",
            'STEP_STATUS_CHANGED',
            Auth::id(),
            $step->task_id,
            $step->id
        );
    }

    /**
     * Generic method to create a transaction record
     */
    private function createTransaction($content, $action, $user_id, $task_id = null, $step_id = null, $department_id = null)
    {
        return Transaction::create([
            'content' => $content,
            'user_id' => $user_id,
            'task_id' => $task_id,
            'step_id' => $step_id,
            'department_id' => $department_id,
        ]);
    }
}
