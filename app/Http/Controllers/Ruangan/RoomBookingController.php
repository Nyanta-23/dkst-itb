<?php

namespace App\Http\Controllers\Ruangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ruangan\StoreRoomBookingRequest;
use App\Models\Room;
use App\Models\RoomBooking;
use App\Models\User;
use App\Notifications\Ruangan\BookingSubmitted;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;
use Inertia\Response;

class RoomBookingController extends Controller
{
    /**
     * Show the form for creating a new booking.
     */
    public function create(Request $request): Response
    {
        Gate::authorize('create', RoomBooking::class);

        return Inertia::render('ruangan/booking-create', [
            'rooms' => Room::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'capacity']),
            'prefill' => [
                'roomId' => $request->integer('room_id') ?: null,
                'date' => $request->string('date')->toString() ?: null,
                'startTime' => $request->string('start_time')->toString() ?: null,
            ],
        ]);
    }

    /**
     * Store a newly created booking, then notify every approver.
     */
    public function store(StoreRoomBookingRequest $request): RedirectResponse
    {
        $booking = RoomBooking::create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'status' => 'pending',
        ]);

        $approvers = User::permission('ruangan.approve')->get();

        Notification::send($approvers, new BookingSubmitted($booking));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Pengajuan booking ruangan berhasil dikirim.']);

        return to_route('ruangan.my-bookings');
    }

    /**
     * Display the bookings owned by the current user.
     */
    public function myBookings(Request $request): Response
    {
        $bookings = RoomBooking::query()
            ->where('user_id', $request->user()->id)
            ->with(['room:id,name', 'approver:id,name'])
            ->latest('booking_date')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (RoomBooking $booking) => [
                'id' => $booking->id,
                'room' => $booking->room->name,
                'date' => $booking->booking_date->toDateString(),
                'startTime' => substr($booking->start_time, 0, 5),
                'endTime' => substr($booking->end_time, 0, 5),
                'purpose' => $booking->purpose,
                'participantCount' => $booking->participant_count,
                'status' => $booking->status,
                'approver' => $booking->approver?->name,
                'approvalNotes' => $booking->approval_notes,
                'canCancel' => Gate::allows('cancel', $booking),
            ]);

        return Inertia::render('ruangan/booking-saya', [
            'bookings' => $bookings,
        ]);
    }

    /**
     * Cancel the given booking (only while still pending, by its owner).
     */
    public function cancel(RoomBooking $booking): RedirectResponse
    {
        Gate::authorize('cancel', $booking);

        $booking->update(['status' => 'cancelled']);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Booking berhasil dibatalkan.']);

        return back();
    }
}
