<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    public function user(){

        return $this->belongsTo(User::class,'user_id');
    }

    public function tasks(){

        return $this->hasMany(Task::class,'board_id');
    }
}
