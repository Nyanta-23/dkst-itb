<?php

namespace App\Policies;

use App\Models\IpAsset;
use App\Models\User;

class IpAssetPolicy
{
    /**
     * Determine whether the user can create IP assets.
     */
    public function create(User $user): bool
    {
        return $user->can('teknologi.manage');
    }

    /**
     * Determine whether the user can update the given IP asset.
     */
    public function update(User $user, IpAsset $ipAsset): bool
    {
        return $user->can('teknologi.manage');
    }

    /**
     * Determine whether the user can delete the given IP asset.
     */
    public function delete(User $user, IpAsset $ipAsset): bool
    {
        return $user->can('teknologi.manage');
    }
}
