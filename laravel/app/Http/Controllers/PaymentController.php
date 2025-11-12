<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Transaction;
use App\Services\PaymentService;
use App\Services\XRPLService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
        private XRPLService $xrplService
    ) {}

    public function checkout(Request $request, Listing $listing)
    {
        if ($listing->status !== 'active') {
            return redirect()->back()->with('error', 'この出品は購入できません');
        }

        $planId = $request->query('plan_id');
        $selectedPlan = $listing->pricePlans()->find($planId);

        if (!$selectedPlan) {
            return redirect()->back()->with('error', '選択されたプランが見つかりません');
        }

        return view('payments.checkout', compact('listing', 'selectedPlan'));
    }

    public function process(Request $request, Listing $listing)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'plan_id' => 'required|exists:listing_price_plans,id',
            'payment_type' => 'required|in:stripe,xrpl',
        ]);

        $selectedPlan = $listing->pricePlans()->find($request->plan_id);

        if (!$selectedPlan) {
            return redirect()->back()->with('error', '選択されたプランが見つかりません');
        }

        try {
            $transaction = $this->paymentService->createTransaction(
                $listing,
                $request->user(),
                $request->payment_method,
                $selectedPlan
            );

            return redirect()->route('transactions.show', $transaction)
                ->with('success', '購入が完了しました');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '決済に失敗しました: ' . $e->getMessage());
        }
    }

    public function processXRPL(Request $request, Listing $listing)
    {
        $request->validate([
            'plan_id' => 'required|exists:listing_price_plans,id',
            'buyer_xrpl_address' => 'required|string',
        ]);

        $selectedPlan = $listing->pricePlans()->find($request->plan_id);

        if (!$selectedPlan) {
            return redirect()->back()->with('error', '選択されたプランが見つかりません');
        }

        if ($listing->status !== 'active') {
            return redirect()->back()->with('error', 'この出品は購入できません');
        }

        try {
            $transaction = $this->paymentService->createXRPLTransaction(
                $listing,
                $request->user(),
                $selectedPlan,
                $request->buyer_xrpl_address
            );

            return redirect()->route('transactions.show', $transaction)
                ->with('success', 'XRPLエスクローが作成されました。指定のアドレスにXRPを送信してください。');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'XRPLエスクローの作成に失敗しました: ' . $e->getMessage());
        }
    }

    public function setupIntent(Request $request)
    {
        return $request->user()->createSetupIntent();
    }

    public function getXRPPrice()
    {
        $rate = $this->xrplService->getXRPPriceInJPY();
        
        return response()->json([
            'rate' => $rate,
            'currency' => 'JPY',
        ]);
    }
}
