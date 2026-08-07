<?php

namespace App\Models;

use App\Enums\BoardMemberRole;
use Database\Factories\BoardMemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BoardMember extends Pivot
{
    /** @use HasFactory<BoardMemberFactory> */
    use HasFactory;

    protected $table = 'board_members';

    public $incrementing = true;

    protected function casts(): array
    {
        return [
            'role' => BoardMemberRole::class,
        ];
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
