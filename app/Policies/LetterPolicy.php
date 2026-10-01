<?php

namespace App\Policies;

use App\Models\Letter;
use App\Models\User;

/**
 * Hak akses surat: sekretariat (surat.manage) mengelola dan melihat semua surat,
 * direktur & super-admin melihat semua surat, user lain hanya melihat surat yang
 * didisposisikan ke unit atau dirinya sendiri.
 */
class LetterPolicy
{
    /**
     * Determine whether the user can view any letters.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('surat.view');
    }

    /**
     * Determine whether the user can view the given letter.
     */
    public function view(User $user, Letter $letter): bool
    {
        if (! $user->can('surat.view')) {
            return false;
        }

        if ($user->can('surat.manage') || $user->hasRole(['direktur', 'super-admin'])) {
            return true;
        }

        return $letter->dispositions
            ->contains(fn ($disposition) => $disposition->to_unit_id === $user->unit_id || $disposition->to_user_id === $user->id);
    }

    /**
     * Determine whether the user can create letters.
     */
    public function create(User $user): bool
    {
        return $user->can('surat.manage');
    }

    /**
     * Determine whether the user can update the letter.
     */
    public function update(User $user, Letter $letter): bool
    {
        return $user->can('surat.manage');
    }

    /**
     * Determine whether the user can delete the letter.
     */
    public function delete(User $user, Letter $letter): bool
    {
        return $user->can('surat.manage');
    }
}
