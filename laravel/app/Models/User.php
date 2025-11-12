<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    use Notifiable, Billable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'xrpl_address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function listings()
    {
        return $this->hasMany(Listing::class, 'seller_id');
    }

    public function purchases()
    {
        return $this->hasMany(Transaction::class, 'buyer_id');
    }

    public function sales()
    {
        return $this->hasMany(Transaction::class, 'seller_id');
    }
}
