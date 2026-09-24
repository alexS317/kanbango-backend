<?php

namespace App\Models;

use Database\Factories\BoardFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['title'])]
class Board extends Model
{
    /** @use HasFactory<BoardFactory> */
    use HasFactory;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'board_members');
    }

    public function members(): HasMany
    {
        return $this->hasMany(BoardMember::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(BoardCategory::class);
    }

    public function memberHasRole(User $user, array $roles): bool
    {
        return $this->members()->where('user_id', $user->id)->whereIn('role', $roles)->exists();
    }
}
