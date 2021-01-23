<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class GiftItem extends Model
{
    protected $table = 'gift_items';
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
        return $this->belongsTo('App\Gift', 'gift_id');
    }

    public function Card()
    {
        return $this->hasOne('App\ScratchCard', 'code' , 'code');
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