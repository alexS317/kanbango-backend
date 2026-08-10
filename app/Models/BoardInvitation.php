<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['email', 'role', 'token', 'board_id', 'invited_by', 'expires_at'])]
class BoardInvitation extends Model
{
    use Prunable;

    public function prunable()
    {
        return static::where('expires_at', '<=', now());
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }
}
