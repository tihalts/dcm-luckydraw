<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LuckyDraw extends Model 
{

    protected $table = 'lucky_draws';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'no_of_winners',
        'is_allow_old_winders',
        'is_allow_repeat_user',
        'start_at',
        'end_at',
        'is_winner_selected',
        'user_id',
        'status' 
    ]; 
}