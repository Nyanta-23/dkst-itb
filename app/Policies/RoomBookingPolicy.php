<?php

namespace App\Policies;

use App\Models\RoomBooking;
use App\Models\User;

/**
 * Hak akses pemesanan ruangan: pemohon mengajukan dan mengelola booking
 * miliknya sendiri, ruangan.approve menyetujui/menolak booking siapa pun.
 */
class RoomBookingPolicy
{
    /**
     * Determine whether the user can create a booking.
     */
    public function create(User $user): bool
    {
        return $user->can('ruangan.book');
    }

    /**
     * Determine whether the user can view the given booking.
     */
    public function view(User $user, RoomBooking $booking): bool
    {
        return $booking->user_id === $user->id
            || $user->can('ruangan.approve')
            || $user->can('ruangan.manage');
    }

    /**
     * Determine whether the user can cancel the given booking (only while
     * still pending, and only by the person who made it).
     */
    public function cancel(User $user, RoomBooking $booking): bool
    {
        return $booking->user_id === $user->id && $booking->status === 'pending';
    }

    /**
     * Determine whether the user can approve or reject the given booking.
     */
    public function review(User $user, RoomBooking $booking): bool
    {
        return $user->can('ruangan.approve') && $booking->status === 'pending';
    }
}
