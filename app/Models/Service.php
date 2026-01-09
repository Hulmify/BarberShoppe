<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['shop_id', 'name', 'description', 'duration_minutes', 'price'];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
