<?php

namespace App\Policies;

use App\Models\Technology;
use App\Models\User;

class TechnologyPolicy
{
    /**
     * Determine whether the user can view any technologies.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('teknologi.view');
    }

    /**
     * Determine whether the user can view the given technology.
     */
    public function view(User $user, Technology $technology): bool
    {
        return $user->can('teknologi.view');
    }

    /**
     * Determine whether the user can create technologies.
     */
    public function create(User $user): bool
    {
        return $user->can('teknologi.manage');
    }

    /**
     * Determine whether the user can update the given technology.
     */
    public function update(User $user, Technology $technology): bool
    {
        return $user->can('teknologi.manage');
    }
}
