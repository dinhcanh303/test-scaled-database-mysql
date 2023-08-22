<?php

namespace App\ModelShards;

use App\Models\User_has_board;
use Database\Supports\ModelShard;

class Board extends ModelShard
{
    protected static $relationships = [
        'user_has_boards' => [User::class, User_has_board::class]
    ];
}
