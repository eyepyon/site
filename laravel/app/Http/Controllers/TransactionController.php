<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = $request->user()
            ->purchases()
            ->with(['listing', 'seller'])
            ->latest()
            ->paginate(20);

        return view('transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $this->authorize('view', $transaction);
        return view('transactions.show', compact('transaction'));
    }

    public function release(Transaction $transaction)
    {
        $this->authorize('release', $transaction);

        if ($transaction->status !== 'escrowed') {
            return redirect()->back()->with('error', 'この取引は解放できません');
        }

        $transaction->update([
            'status' => 'released',
            'released_at' => now(),
        ]);

        return redirect()->back()->with('success', '支払いを解放しました');
    }
}
