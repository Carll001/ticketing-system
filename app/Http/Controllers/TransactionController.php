<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * Display all transactions.
     */
    public function index()
    {
        $transactions = Transaction::with(['user', 'task', 'step', 'department'])
            ->latest()
            ->get();

        return Inertia::render('Transaction/Index', [
            'transactions' => $transactions,
        ]);
    }

    /**
     * Show transactions for a specific task.
     */
    public function forTask(Task $task)
    {
        $transactions = Transaction::with(['user', 'step', 'department'])
            ->where('task_id', $task->id)
            ->latest()
            ->get();

        return Inertia::render('Transaction/TaskTransactions', [
            'task' => $task,
            'transactions' => $transactions,
        ]);
    }

    /**
     * Store a new transaction for a task or step.
     */
    public function store(Request $request, Task $task)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
            'step_id' => 'nullable|exists:steps,id',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $transaction = Transaction::create([
            'content' => $validated['content'],
            'task_id' => $task->id,
            'step_id' => $validated['step_id'] ?? null,
            'department_id' => $validated['department_id'] ?? null,
            'user_id' => Auth::id(),
        ]);

        return back()->with('success', 'Transaction created successfully.');
    }

    /**
     * Display a single transaction.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'task', 'step', 'department']);

        return Inertia::render('Transaction/Show', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Show the edit form for a transaction.
     */
    public function edit(Transaction $transaction)
    {
        $transaction->load(['user', 'task', 'step', 'department']);

        return Inertia::render('Transaction/Edit', [
            'transaction' => $transaction,
        ]);
    }

    /**
     * Update a transaction.
     */
    public function update(Request $request, Transaction $transaction)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $transaction->update([
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Transaction updated successfully.');
    }

    /**
     * Delete a transaction.
     */
    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return back()->with('success', 'Transaction deleted successfully.');
    }
}
