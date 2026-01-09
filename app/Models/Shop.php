<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'slug', 'custom_domain', 'description', 'logo_path', 'primary_color', 'currency', 'off_date', 'timezone', 'logo'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function stylists()
    {
        return $this->hasMany(Stylist::class)->orderBy('display_order');
    }
}
