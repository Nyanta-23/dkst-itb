<?php

namespace App\Policies;

use App\Models\Partner;
use App\Models\User;

class PartnerPolicy
{
    /**
     * Determine whether the user can view any partners.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('mitra.view');
    }

    /**
     * Determine whether the user can view the given partner.
     */
    public function view(User $user, Partner $partner): bool
    {
        return $user->can('mitra.view');
    }

    /**
     * Determine whether the user can create partners.
     */
    public function create(User $user): bool
    {
        return $user->can('mitra.manage');
    }

    /**
     * Determine whether the user can update the given partner.
     */
    public function update(User $user, Partner $partner): bool
    {
        return $user->can('mitra.manage');
    }

    /**
     * Determine whether the user can delete the given partner.
     *
     * The controller separately checks for active deals and reports that
     * in Indonesian via a flash message, since that is a business rule
     * rather than an authorization concern.
     */
    public function delete(User $user, Partner $partner): bool
    {
        return $user->can('mitra.manage');
    }
}
