<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'listing_id',
        'listing_price_plan_id',
        'buyer_id',
        'seller_id',
        'amount',
        'platform_fee',
        'stripe_payment_intent_id',
        'status',
        'paid_at',
        'released_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'paid_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function pricePlan()
    {
        return $this->belongsTo(ListingPricePlan::class, 'listing_price_plan_id');
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
