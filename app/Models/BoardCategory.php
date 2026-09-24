<?php

namespace App\Models;

use Database\Factories\BoardCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['board_id', 'name', 'position'])]
class BoardCategory extends Model
{
    /** @use HasFactory<BoardCategoryFactory> */
    use HasFactory;

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }
}
