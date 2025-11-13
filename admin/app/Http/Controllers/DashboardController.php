<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 統計情報
        $stats = [
            'total_users' => User::count(),
            'total_listings' => Listing::count(),
            'active_listings' => Listing::where('status', 'active')->count(),
            'sold_listings' => Listing::where('status', 'sold')->count(),
            'total_transactions' => Transaction::count(),
            'total_revenue' => Transaction::where('status', 'released')->sum('amount'),
            'platform_revenue' => Transaction::where('status', 'released')->sum('platform_fee'),
        ];

        // 最近の取引
        $recent_transactions = Transaction::with(['listing', 'buyer', 'seller'])
            ->latest()
            ->take(10)
            ->get();

        // 決済方法別の統計
        $payment_stats = Transaction::select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        // 月別売上
        $monthly_revenue = Transaction::where('status', 'released')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('sum(amount) as revenue'),
                DB::raw('sum(platform_fee) as fee')
            )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        return view('dashboard', compact('stats', 'recent_transactions', 'payment_stats', 'monthly_revenue'));
    }
}
