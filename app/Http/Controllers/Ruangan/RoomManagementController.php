<?php

namespace App\Http\Controllers\Ruangan;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ruangan\StoreRoomRequest;
use App\Http\Requests\Ruangan\UpdateRoomRequest;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

class RoomManagementController extends Controller
{
    /**
     * Display every room for management.
     */
    public function index(): Response
    {
        Gate::authorize('create', Room::class);

        return Inertia::render('ruangan/kelola', [
            'rooms' => Room::query()->orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created room.
     */
    public function store(StoreRoomRequest $request): RedirectResponse
    {
        Room::create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Ruangan berhasil ditambahkan.']);

        return back();
    }

    /**
     * Update the given room.
     */
    public function update(UpdateRoomRequest $request, Room $room): RedirectResponse
    {
        $room->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Ruangan berhasil diperbarui.']);

        return back();
    }

    /**
     * Toggle the active state of the given room.
     */
    public function toggle(Room $room): RedirectResponse
    {
        Gate::authorize('update', $room);

        $room->update(['is_active' => ! $room->is_active]);

        Inertia::flash('toast', ['type' => 'success', 'message' => $room->is_active ? 'Ruangan diaktifkan.' : 'Ruangan dinonaktifkan.']);

        return back();
    }

    /**
     * Remove the given room.
     */
    public function destroy(Room $room): RedirectResponse
    {
        Gate::authorize('delete', $room);

        $room->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Ruangan berhasil dihapus.']);

        return back();
    }
}
