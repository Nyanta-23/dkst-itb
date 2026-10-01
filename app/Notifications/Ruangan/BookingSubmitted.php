<?php

namespace App\Notifications\Ruangan;

use App\Models\RoomBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifikasi database ke setiap user ber-permission ruangan.approve saat
 * ada pengajuan booking ruangan baru.
 */
class BookingSubmitted extends Notification
{
    use Queueable;

    public function __construct(private readonly RoomBooking $booking) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, string>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Pengajuan booking ruangan baru',
            'message' => "{$this->booking->user->name} mengajukan \"{$this->booking->room->name}\" pada {$this->booking->booking_date->toDateString()}.",
            'url' => route('ruangan.approvals.index'),
            'icon' => 'CalendarCheck',
        ];
    }
}
