<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;
use NotificationChannels\WebPush\WebPushChannel;

class NewBookingReceived extends Notification
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
        $customerName = $this->booking->customer->name;
        $serviceName = $this->booking->items->first()->service->name ?? 'Service';
        
        $timezone = $this->booking->shop->timezone ?? config('app.timezone');
        $localTime = $this->booking->start_time->setTimezone($timezone);
        $time = $localTime->format('h:i A');
        $date = $localTime->toDateString();

        $url = route('admin.appointments.index', ['date' => $date, 'highlight' => $this->booking->id]);

        return (new WebPushMessage)
            ->title('New Appointment!')
            ->icon('/pwa-192x192.png')
            ->body("{$customerName} booked {$serviceName} for {$time}.")
            ->action('View details', 'view_appointment')
            ->data(['url' => $url]);
    }

    public function toArray($notifiable)
    {
        $customerName = $this->booking->customer->name;
        $serviceName = $this->booking->items->first()->service->name ?? 'Service';
        
        $timezone = $this->booking->shop->timezone ?? config('app.timezone');
        $localTime = $this->booking->start_time->setTimezone($timezone);
        $time = $localTime->format('h:i A');
        $date = $localTime->toDateString();

        $url = route('admin.appointments.index', ['date' => $date, 'highlight' => $this->booking->id]);

        return [
            'title' => 'New Appointment!',
            'body' => "{$customerName} booked {$serviceName} for {$time}.",
            'data' => [
                'url' => $url,
                'booking_id' => $this->booking->id
            ]
        ];
    }
}
