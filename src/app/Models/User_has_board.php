<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_has_board extends Model 
{
    protected $fillable = [
        'user_id',
        'board_id',
    ];

}
