<?php

namespace App\Policies;

use App\Models\IndicatorRealization;
use App\Models\ProgramIndicator;
use App\Models\User;

/**
 * Hak akses realisasi indikator: program.report hanya untuk indikator milik
 * program di unitnya sendiri, program.verify untuk memverifikasi/menolak
 * realisasi apa pun.
 */
class IndicatorRealizationPolicy
{
    /**
     * Determine whether the user can report a realization for the given indicator.
     */
    public function create(User $user, ProgramIndicator $indicator): bool
    {
        return $user->can('program.report') && $indicator->program->unit_id === $user->unit_id;
    }

    /**
     * Determine whether the user can edit/resubmit the given realization
     * (only while it is still rejected, and only for their own unit's program).
     */
    public function update(User $user, IndicatorRealization $realization): bool
    {
        return $user->can('program.report')
            && $realization->status === 'rejected'
            && $realization->indicator->program->unit_id === $user->unit_id;
    }

    /**
     * Determine whether the user can verify or reject the given realization.
     */
    public function review(User $user, IndicatorRealization $realization): bool
    {
        return $user->can('program.verify') && $realization->status === 'submitted';
    }
}
