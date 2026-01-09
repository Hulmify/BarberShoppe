<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = ['shop_id', 'customer_id', 'stylist_id', 'start_time', 'end_time', 'total_price', 'status', 'notes'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(BookingItem::class);
    }

    public function stylist()
    {
        return $this->belongsTo(Stylist::class);
    }
}
