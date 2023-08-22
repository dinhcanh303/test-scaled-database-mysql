<?php

namespace App\ModelShards;

use Database\Supports\ModelShard;

class Post extends ModelShard
{
    protected static $relationships = [
        'board_has_posts' => [Board::class,Board_has_post::class]
    ];
}
