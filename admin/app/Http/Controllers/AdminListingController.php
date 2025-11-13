<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;

class AdminListingController extends Controller
{
    public function index(Request $request)
    {
        $query = Listing::with('seller');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        $listings = $query->latest()->paginate(20);

        return view('listings.index', compact('listings'));
    }

    public function show(Listing $listing)
    {
        $listing->load(['seller', 'pricePlans', 'transactions.buyer']);
        
        return view('listings.show', compact('listing'));
    }

    public function updateStatus(Request $request, Listing $listing)
    {
        $request->validate([
            'status' => 'required|in:draft,active,sold,suspended',
        ]);

        $listing->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'ステータスを更新しました');
    }

    public function destroy(Listing $listing)
    {
        $listing->delete();
        
        return redirect()->route('admin.listings.index')
            ->with('success', '出品を削除しました');
    }
}
