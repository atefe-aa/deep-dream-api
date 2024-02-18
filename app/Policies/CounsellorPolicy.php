<?php

namespace App\Policies;

use App\Models\Counsellor;
use App\Models\User;

class CounsellorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Counsellor $counsellor): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Counsellor $counsellor): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Counsellor $counsellor): bool
    {
        // Check if the user is a super admin or an operator
        if ($user->hasRole(['superAdmin', 'operator'])) {
            return true;
        }

        // Check if the user is the creator of the test
        return $user->id === $counsellor->laboratory->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Counsellor $counsellor): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Counsellor $counsellor): bool
    {
        //
    }
}
