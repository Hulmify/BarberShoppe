<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class BookingConfirmed extends Notification
{
    use Queueable;

    protected $booking;

    /**
     * Create a new notification instance.
     */
    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    /**
     * Get the web push representation of the notification.
     *
     * @param  mixed  $notifiable
     * @param  mixed  $notification
     * @return \NotificationChannels\WebPush\WebPushMessage
     */
    public function toWebPush($notifiable, $notification)
    {
        $shopName = $this->booking->shop->name ?? 'BarberShoppe';
        $time = $this->booking->start_time->format('h:i A');

        return (new WebPushMessage)
            ->title('Booking Confirmed!')
            ->icon('/pwa-192x192.png')
            ->body("Your appointment at {$shopName} is set for {$time}.")
            ->action('View My Appointments', 'view_appointments')
            ->data(['url' => route('booking.my_appointments', [
                'slug' => $this->booking->shop->slug,
                'phone' => $this->booking->customer->phone
            ])]);
    }
}
