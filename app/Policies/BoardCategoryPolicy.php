<?php

namespace App\Policies;

use App\Enums\BoardMemberRole;
use App\Models\Board;
use App\Models\BoardCategory;
use App\Models\User;

class BoardCategoryPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Board $board): bool
    {
        return $board->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, BoardCategory $boardCategory): bool
    {
        return $boardCategory->board->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Board $board): bool
    {
        return $board->userHasRole($user, [BoardMemberRole::OWNER]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BoardCategory $boardCategory): bool
    {
        return $boardCategory->board->userHasRole($user, [BoardMemberRole::OWNER]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BoardCategory $boardCategory): bool
    {
        return $boardCategory->board->userHasRole($user, [BoardMemberRole::OWNER]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BoardCategory $boardCategory): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BoardCategory $boardCategory): bool
    {
        return false;
    }
}
