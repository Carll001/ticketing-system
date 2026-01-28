<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */

   public function index(Request $request)
{
    $search = $request->input('search');

    $transactions = Transaction::with(['user', 'task'])
        ->when($search, function ($query, $search) {
            $query->where('transaction_number', 'ILIKE', "%{$search}%")
                  ->orWhere('content', 'ILIKE', "%{$search}%")
                  ->orWhereHas('user', function ($q) use ($search) {
                      $q->where('name', 'ILIKE', "%{$search}%");
                  });
        })
        ->get();

    return Inertia::render('Transaction/Index', [
        'transactions' => $transactions,
        'filters' => ['search' => $search],
    ]);
}


    // public function index()
    // {
    //     $transactions = Transaction::with(['user', 'task'])->get();
    //     return Inertia::render('Transaction/Index',[
    //         'transactions' => $transactions
    //     ]);
    // }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transaction $transaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transaction $transaction)
    {
        //
    }
}
