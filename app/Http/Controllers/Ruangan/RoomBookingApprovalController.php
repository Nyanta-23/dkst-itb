<?php

namespace App\Http\Controllers\Ruangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ruangan\ReviewRoomBookingRequest;
use App\Models\RoomBooking;
use App\Notifications\Ruangan\BookingReviewed;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class RoomBookingApprovalController extends Controller
{
    /**
     * Display bookings awaiting, approved, or rejected by the current reviewer.
     */
    public function index(Request $request): Response
    {
        Gate::authorize('ruangan.approve');

        $tab = in_array($request->string('tab')->toString(), ['pending', 'approved', 'rejected'], true)
            ? $request->string('tab')->toString()
            : 'pending';

        $bookings = RoomBooking::query()
            ->where('status', $tab)
            ->with(['room:id,name', 'user:id,name', 'approver:id,name'])
            ->orderBy($tab === 'pending' ? 'booking_date' : 'approved_at', $tab === 'pending' ? 'asc' : 'desc')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (RoomBooking $booking) => [
                'id' => $booking->id,
                'room' => $booking->room->name,
                'user' => $booking->user->name,
                'date' => $booking->booking_date->toDateString(),
                'startTime' => substr($booking->start_time, 0, 5),
                'endTime' => substr($booking->end_time, 0, 5),
                'purpose' => $booking->purpose,
                'participantCount' => $booking->participant_count,
                'status' => $booking->status,
                'approver' => $booking->approver?->name,
                'approvalNotes' => $booking->approval_notes,
            ]);

        return Inertia::render('ruangan/persetujuan', [
            'bookings' => $bookings,
            'tab' => $tab,
        ]);
    }

    /**
     * Approve or reject the given booking.
     */
    public function update(ReviewRoomBookingRequest $request, RoomBooking $booking): RedirectResponse
    {
        $booking->update([
            'status' => $request->string('action')->toString(),
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
            'approval_notes' => $request->string('approval_notes')->toString() ?: null,
        ]);

        $booking->user->notify(new BookingReviewed($booking));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Keputusan booking berhasil disimpan.']);

        return back();
    }
}
