<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;

/**
 * Hak akses program: subdit-program (program.manage) mengelola semua program,
 * direktur/subdit-keuangan/program.verify melihat semua program, unit lain
 * hanya melihat program milik unitnya sendiri.
 */
class ProgramPolicy
{
    /**
     * Determine whether the user can view any programs.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('program.view');
    }

    /**
     * Determine whether the user can view the given program.
     */
    public function view(User $user, Program $program): bool
    {
        if (! $user->can('program.view')) {
            return false;
        }

        if ($user->can('program.manage') || $user->can('program.verify') || $user->hasRole(['direktur', 'subdit-keuangan', 'super-admin'])) {
            return true;
        }

        return $program->unit_id === $user->unit_id;
    }

    /**
     * Determine whether the user can create programs.
     */
    public function create(User $user): bool
    {
        return $user->can('program.manage');
    }

    /**
     * Determine whether the user can update the given program.
     */
    public function update(User $user, Program $program): bool
    {
        return $user->can('program.manage');
    }
}
