<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = ['shop_id', 'stylist_id', 'day_of_week', 'start_time', 'end_time', 'is_active'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function stylist()
    {
        return $this->belongsTo(Stylist::class);
    }
}
