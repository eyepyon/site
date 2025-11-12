<?php

namespace App\Services;

use App\Models\Listing;
use App\Models\Transaction;
use App\Models\User;

class PaymentService
{
    private const PLATFORM_FEE_RATE = 0.10; // 10%手数料

    public function createTransaction(Listing $listing, User $buyer, string $paymentMethod, $pricePlan): Transaction
    {
        $amount = $pricePlan->price;
        $platformFee = $amount * self::PLATFORM_FEE_RATE;

        // Stripe決済を実行
        $paymentIntent = $buyer->charge(
            $amount * 100, // Stripeは最小通貨単位で処理
            $paymentMethod,
            [
                'description' => "購入: {$listing->title} - {$pricePlan->name}",
                'metadata' => [
                    'listing_id' => $listing->id,
                    'buyer_id' => $buyer->id,
                    'plan_id' => $pricePlan->id,
                    'plan_name' => $pricePlan->name,
                ],
            ]
        );

        // トランザクション作成
        $transaction = Transaction::create([
            'listing_id' => $listing->id,
            'listing_price_plan_id' => $pricePlan->id,
            'buyer_id' => $buyer->id,
            'seller_id' => $listing->seller_id,
            'amount' => $amount,
            'platform_fee' => $platformFee,
            'stripe_payment_intent_id' => $paymentIntent->id,
            'status' => 'escrowed',
            'paid_at' => now(),
        ]);

        // 出品を売却済みに更新
        $listing->update(['status' => 'sold']);

        return $transaction;
    }

    public function releasePayment(Transaction $transaction): void
    {
        $sellerAmount = $transaction->amount - $transaction->platform_fee;

        // 実際の実装では、Stripe Connectを使用して売り手に送金
        // ここでは簡略化

        $transaction->update([
            'status' => 'released',
            'released_at' => now(),
        ]);
    }
}
