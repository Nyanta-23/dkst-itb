<?php

namespace App\Policies;

use App\Models\Deal;
use App\Models\User;

class DealPolicy
{
    /**
     * Determine whether the user can create deals.
     */
    public function create(User $user): bool
    {
        return $user->can('teknologi.manage');
    }

    /**
     * Determine whether the user can update the given deal.
     */
    public function update(User $user, Deal $deal): bool
    {
        return $user->can('teknologi.manage');
    }
}
