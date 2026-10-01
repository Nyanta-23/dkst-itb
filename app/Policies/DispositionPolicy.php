<?php

namespace App\Policies;

use App\Models\Disposition;
use App\Models\Letter;
use App\Models\User;

/**
 * Hak akses disposisi: direktur & super-admin bisa mendisposisikan surat apa pun,
 * deputi (surat.dispose) hanya bisa membuat disposisi lanjutan atas surat yang
 * sudah didisposisikan ke unit atau dirinya. Status hanya bisa diubah oleh
 * penerima disposisi.
 */
class DispositionPolicy
{
    /**
     * Determine whether the user can create a disposition for the given letter.
     */
    public function create(User $user, Letter $letter): bool
    {
        if (! $user->can('surat.dispose')) {
            return false;
        }

        if ($user->hasRole(['direktur', 'super-admin'])) {
            return true;
        }

        return $letter->dispositions
            ->contains(fn (Disposition $disposition) => $disposition->to_unit_id === $user->unit_id || $disposition->to_user_id === $user->id);
    }

    /**
     * Determine whether the user can update the status of the given disposition
     * (menandai diproses/selesai). Only the recipient may do so.
     */
    public function updateStatus(User $user, Disposition $disposition): bool
    {
        return $disposition->isRecipient($user);
    }
}
