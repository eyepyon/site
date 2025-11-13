<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingPricePlan extends Model
{
    protected $fillable = [
        'listing_id',
        'name',
        'description',
        'price',
        'includes_members',
        'includes_source',
        'includes_installation',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'includes_members' => 'boolean',
        'includes_source' => 'boolean',
        'includes_installation' => 'boolean',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
