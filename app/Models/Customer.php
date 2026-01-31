<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use NotificationChannels\WebPush\HasPushSubscriptions;
use Illuminate\Notifications\Notifiable;

class Customer extends Model
{
    use HasFactory, Notifiable, HasPushSubscriptions;

    protected $fillable = ['name', 'email', 'phone'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
