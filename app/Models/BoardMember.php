<?php

namespace App\Models;

use Database\Factories\BoardMemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class BoardMember extends Pivot
{
    /** @use HasFactory<BoardMemberFactory> */
    use HasFactory;

    protected $table = 'board_members';
}
