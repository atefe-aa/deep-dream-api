<?php

namespace App\Policies;

use App\Models\Test;
use App\Models\User;

class TestPolicy
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
    public function view(User $user, Test $test): bool
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
    public function update(User $user, Test $test): bool
    {
        // Check if the user is a super admin or an operator
        if ($user->hasRole(['superAdmin', 'operator'])) {
            return true;
        }

        // Check if the user is the creator of the test
        return $user->id === $test->laboratory->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Test $test): bool
    {
        // Check if the user is a super admin or an operator
        if ($user->hasRole(['superAdmin', 'operator'])) {
            return true;
        }

        // Check if the user is the creator of the test
        return $user->id === $test->laboratory->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Test $test): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Test $test): bool
    {
        //
    }
}
