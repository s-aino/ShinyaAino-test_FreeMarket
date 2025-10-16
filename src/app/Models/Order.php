<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    const STATUS_PENDING  = 'pending';
    const STATUS_PAID     = 'paid';
    const STATUS_CANCELED = 'canceled';

    protected $fillable = [
        'buyer_id',
        'item_id',
        'address_id',
        'price',
        'qty',
        'status',
        'ordered_at',
    ];

    protected $dates = ['ordered_at'];
    protected $casts = ['ordered_at' => 'datetime'];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
