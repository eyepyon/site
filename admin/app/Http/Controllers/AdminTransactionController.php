<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class AdminTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['listing', 'buyer', 'seller']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->payment_method) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->search) {
            $query->whereHas('listing', function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%");
            });
        }

        $transactions = $query->latest()->paginate(20);

        return view('transactions.index', compact('transactions'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['listing.pricePlans', 'pricePlan', 'buyer', 'seller']);
        
        return view('transactions.show', compact('transaction'));
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,escrowed,released,refunded',
        ]);

        $updateData = ['status' => $request->status];

        if ($request->status === 'released' && !$transaction->released_at) {
            $updateData['released_at'] = now();
        }

        $transaction->update($updateData);

        return redirect()->back()->with('success', 'ステータスを更新しました');
    }
}
