<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SpinGiftItem extends Model
{
    protected $table = 'spin_gift_items';
    public $timestamps = true;

    protected $fillable = [
        'code',
        'gift_at',
        'gifted_at',
        'gift_id',
        'status'
    ];

    // protected $dates = [
    //     'start_at' , 'end_at'
    // ];

    public function Gift()
    {
        return $this->belongsTo('App\SpinGift', 'gift_id');
    }

    public function Winner()
    {
        return $this->hasOne('App\SpinWinner', 'code' , 'code');
    }

    public function scopeUnGifted($query)
    {
        return $query->whereNull('gifted_at');
    }

    public function scopeGifted($query)
    {        
        return $query->whereNotNull('gifted_at');
    }
}