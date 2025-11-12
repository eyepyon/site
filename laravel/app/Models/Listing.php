<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $fillable = [
        'seller_id',
        'title',
        'description',
        'type',
        'price',
        'url',
        'tech_stack',
        'monthly_revenue',
        'monthly_profit',
        'monthly_pv',
        'monthly_uu',
        'total_users',
        'dau',
        'mau',
        'total_downloads',
        'status',
    ];

    protected $casts = [
        'tech_stack' => 'array',
        'price' => 'decimal:2',
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function pricePlans()
    {
        return $this->hasMany(ListingPricePlan::class)->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
