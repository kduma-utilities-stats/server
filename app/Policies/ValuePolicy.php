<?php

namespace App\Policies;

use App\Models\Reading;
use App\Models\User;
use App\Models\Value;
use Illuminate\Auth\Access\Response;

class ValuePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Reading $reading): bool
    {
        return $reading->user_id === $user->id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Value $value): bool
    {
        return $value->reading->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Reading $reading): bool
    {
        return $reading->user_id === $user->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Value $value): bool
    {
        return $value->reading->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Value $value): bool
    {
        return $value->reading->user_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Value $value): bool
    {
        return $value->reading->user_id === $user->id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Value $value): bool
    {
        return $value->reading->user_id === $user->id;
    }
}
