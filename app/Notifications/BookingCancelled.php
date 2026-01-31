<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class BookingCancelled extends Notification
{
    use Queueable;

    protected $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class, 'database'];
    }

    public function toWebPush($notifiable, $notification)
    {
        $shopName = $this->booking->shop->name ?? 'BarberShoppe';
        $date = $this->booking->start_time->format('M d');

        return (new WebPushMessage)
            ->title('Appointment Update')
            ->icon('/pwa-192x192.png')
            ->body("Your appointment at {$shopName} on {$date} has been cancelled.")
            ->action('Book New Appointment', 'book_new')
            ->data(['url' => route('booking.kiosk', ['slug' => $this->booking->shop->slug])]);
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Appointment Cancelled',
            'body' => "Your appointment has been cancelled.",
            'booking_id' => $this->booking->id
        ];
    }
}
