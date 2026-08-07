<?php

namespace App\Policies;

use App\Enums\BoardMemberRole;
use App\Models\Board;
use App\Models\BoardMember;
use App\Models\User;

class BoardMemberPolicy
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
    public function view(User $user, BoardMember $boardMember): bool
    {
        return $boardMember->board->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Board $board): bool
    {
        return $this->hasRole($user, $board, [BoardMemberRole::OWNER, BoardMemberRole::ADMIN]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BoardMember $boardMember): bool
    {
        return $this->hasRole($user, $boardMember->board, [BoardMemberRole::OWNER, BoardMemberRole::ADMIN]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BoardMember $boardMember): bool
    {
        // Users can always remove themselves from the board
        if ($user->id === $boardMember->user_id) {
            return $boardMember->role !== BoardMemberRole::OWNER;
        }

        return $this->hasRole($user, $boardMember->board, [BoardMemberRole::OWNER, BoardMemberRole::ADMIN])
            && $boardMember->role !== BoardMemberRole::OWNER;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BoardMember $boardMember): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BoardMember $boardMember): bool
    {
        return false;
    }

    private function hasRole(User $user, Board $board, array $roles): bool
    {
        return $board->members()->where('user_id', $user->id)->whereIn('role', $roles)->exists();
    }
}
