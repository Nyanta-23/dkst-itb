<?php

namespace App\Policies;

use App\Models\Room;
use App\Models\User;

/**
 * Hak akses ruangan: siapa pun dengan ruangan.view bisa melihat daftar dan
 * jadwal ruangan; hanya ruangan.manage yang bisa mengelola data ruangan.
 */
class RoomPolicy
{
    /**
     * Determine whether the user can view any rooms.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('ruangan.view');
    }

    /**
     * Determine whether the user can view the given room.
     */
    public function view(User $user, Room $room): bool
    {
        return $user->can('ruangan.view');
    }

    /**
     * Determine whether the user can create rooms.
     */
    public function create(User $user): bool
    {
        return $user->can('ruangan.manage');
    }

    /**
     * Determine whether the user can update the given room.
     */
    public function update(User $user, Room $room): bool
    {
        return $user->can('ruangan.manage');
    }

    /**
     * Determine whether the user can delete the given room.
     */
    public function delete(User $user, Room $room): bool
    {
        return $user->can('ruangan.manage');
    }
}
