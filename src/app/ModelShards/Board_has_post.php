<?php

namespace App\ModelShards;

use Database\Supports\ModelShard;

class Board_has_post extends ModelShard
{
    protected static  $primaryKey = 'board_id';
    protected static $relationships = [
    ];
}
