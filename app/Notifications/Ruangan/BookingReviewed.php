<?php

namespace App\Notifications\Ruangan;

use App\Models\RoomBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notifikasi database ke pemohon saat booking ruangannya disetujui atau ditolak.
 */
class BookingReviewed extends Notification
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
        $approved = $this->booking->status === 'approved';

        $message = $approved
            ? "Booking \"{$this->booking->room->name}\" pada {$this->booking->booking_date->toDateString()} telah disetujui."
            : "Booking \"{$this->booking->room->name}\" pada {$this->booking->booking_date->toDateString()} ditolak: {$this->booking->approval_notes}";

        return [
            'title' => $approved ? 'Booking ruangan disetujui' : 'Booking ruangan ditolak',
            'message' => $message,
            'url' => route('ruangan.my-bookings'),
            'icon' => 'CalendarCheck',
        ];
    }
}
