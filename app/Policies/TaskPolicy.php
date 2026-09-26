<?php

namespace App\Policies;

use App\Enums\BoardMemberRole;
use App\Models\Board;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, ?Board $board): bool
    {
        return $user->tasks()->exists() ||
        $board->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        return $task->assignedTo?->id === $user->id ||
        $task->board->members()->where('user_id', $user->id)->exists();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Board $board): bool
    {
        return $board->userHasRole($user, [
            BoardMemberRole::OWNER,
            BoardMemberRole::ADMIN,
            BoardMemberRole::EDITOR,
        ]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): bool
    {
        if ($task->createdBy?->id === $user->id) {
            return $task->board->userHasRole($user, [
                BoardMemberRole::OWNER,
                BoardMemberRole::ADMIN,
                BoardMemberRole::EDITOR,
            ]);
        }

        return $task->board->userHasRole($user, [BoardMemberRole::OWNER, BoardMemberRole::ADMIN]);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): bool
    {
        if ($task->createdBy?->id === $user->id) {
            return $task->board->userHasRole($user, [
                BoardMemberRole::OWNER,
                BoardMemberRole::ADMIN,
                BoardMemberRole::EDITOR,
            ]);
        }

        return $task->board->userHasRole($user, [BoardMemberRole::OWNER, BoardMemberRole::ADMIN]);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        return false;
    }
}
