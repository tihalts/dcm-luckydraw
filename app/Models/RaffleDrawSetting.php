<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class RaffleDrawSetting extends Model
{
    protected $table = 'raffle_draw_settings';
    public $timestamps = true;

    protected $fillable = [
        'lucky_draw_id',
        'email',
        'sms',
        'image',
        'data',
        'send_sms',
        'send_email',
        'min_amount',
        'max_points',
        'status'
    ];
}