<?php

namespace App\Http\Controllers\Ruangan;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomBooking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class RoomController extends Controller
{
    /**
     * Display the room list and the daily schedule for the selected date.
     */
    public function index(Request $request): Response
    {
        Gate::authorize('viewAny', Room::class);

        $requestedDate = $request->string('date')->toString();
        $date = $requestedDate !== '' && Carbon::hasFormat($requestedDate, 'Y-m-d')
            ? $requestedDate
            : now()->toDateString();

        $rooms = Room::query()
            ->orderBy('name')
            ->get()
            ->map(fn (Room $room) => [
                'id' => $room->id,
                'name' => $room->name,
                'building' => $room->building,
                'capacity' => $room->capacity,
                'facilities' => $room->facilities,
                'isActive' => $room->is_active,
            ]);

        $bookings = RoomBooking::query()
            ->where('booking_date', $date)
            ->whereIn('status', ['pending', 'approved'])
            ->with('user:id,name')
            ->get()
            ->map(fn (RoomBooking $booking) => [
                'id' => $booking->id,
                'roomId' => $booking->room_id,
                'startTime' => substr($booking->start_time, 0, 5),
                'endTime' => substr($booking->end_time, 0, 5),
                'status' => $booking->status,
                'purpose' => $booking->purpose,
                'user' => $booking->user->name,
            ]);

        return Inertia::render('ruangan/index', [
            'rooms' => $rooms,
            'bookings' => $bookings,
            'date' => $date,
            'can' => [
                'book' => $request->user()->can('ruangan.book'),
            ],
        ]);
    }
}
