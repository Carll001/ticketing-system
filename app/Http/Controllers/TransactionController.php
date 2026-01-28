<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::with(['user', 'task'])->get();

        return Inertia::render('Transaction/Index', [
            'transactions' => $transactions
        ]);
    }

    public function create()
    {
        // optional page kung may create page ka
    }

    public function store(TransactionRequest $request)
    {
        $data = $request->validated();

        Transaction::create([
            ...$data,
            'user_id' => Auth::id(),
 // automatic logged in user
        ]);

        return back();
    }

    public function show(Transaction $transaction)
    {
        return Inertia::render('Transaction/Show', [
            'transaction' => $transaction->load(['user', 'task']),
        ]);
    }

    public function edit(Transaction $transaction)
    {
        return Inertia::render('Transaction/Edit', [
            'transaction' => $transaction,
        ]);
    }

    public function update(TransactionRequest $request, Transaction $transaction)
    {
        $transaction->update($request->validated());

        return back();
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return back();
    }
}
