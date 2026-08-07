<?php

namespace App\Enums;

enum BoardMemberRole: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case EDITOR = 'editor';
    case VIEWER = 'viewer';
}
