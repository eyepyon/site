<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    public function index()
    {
        $listings = Listing::active()
            ->with('seller')
            ->latest()
            ->paginate(20);

        return view('listings.index', compact('listings'));
    }

    public function show(Listing $listing)
    {
        $listing->load('pricePlans');
        return view('listings.show', compact('listing'));
    }

    public function create()
    {
        return view('listings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:website,app,saas',
            'url' => 'nullable|url',
            'monthly_revenue' => 'nullable|integer|min:0',
            'monthly_profit' => 'nullable|integer|min:0',
            'monthly_pv' => 'nullable|integer|min:0',
            'monthly_uu' => 'nullable|integer|min:0',
            'total_users' => 'nullable|integer|min:0',
            'dau' => 'nullable|integer|min:0',
            'mau' => 'nullable|integer|min:0',
            'total_downloads' => 'nullable|integer|min:0',
            'plans' => 'required|array|min:1',
            'plans.*.name' => 'required|string|max:255',
            'plans.*.price' => 'required|numeric|min:0',
            'plans.*.description' => 'nullable|string',
            'plans.*.includes_members' => 'nullable|boolean',
            'plans.*.includes_source' => 'nullable|boolean',
            'plans.*.includes_installation' => 'nullable|boolean',
        ]);

        // 最低価格を取得
        $minPrice = collect($validated['plans'])->min('price');
        $validated['price'] = $minPrice;

        $listing = $request->user()->listings()->create($validated);

        // 価格プランを作成
        foreach ($validated['plans'] as $index => $planData) {
            $listing->pricePlans()->create([
                'name' => $planData['name'],
                'price' => $planData['price'],
                'description' => $planData['description'] ?? null,
                'includes_members' => $planData['includes_members'] ?? false,
                'includes_source' => $planData['includes_source'] ?? false,
                'includes_installation' => $planData['includes_installation'] ?? false,
                'sort_order' => $index,
            ]);
        }

        return redirect()->route('listings.show', $listing)
            ->with('success', '出品を作成しました');
    }

    public function edit(Listing $listing)
    {
        $this->authorize('update', $listing);
        return view('listings.edit', compact('listing'));
    }

    public function update(Request $request, Listing $listing)
    {
        $this->authorize('update', $listing);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:draft,active,suspended',
        ]);

        $listing->update($validated);

        return redirect()->route('listings.show', $listing)
            ->with('success', '出品を更新しました');
    }
}
