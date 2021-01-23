<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    protected $table = 'gifts';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'code',
        'description',
        'image',
        'no_of_gifts',
        'start_at',
        'end_at',
        'campaign_id',
        'status'
    ];

    // protected $dates = [
    //     'start_at' , 'end_at'
    // ];

    public function Campaign()
    {
        return $this->belongsTo('App\Campaign', 'campaign_id');
    }

    public function Items()
    {
        return $this->hasMany('App\GiftItem', 'gift_id')->where("status" , true);
    }

    public function FreeGifts()
    {
        return $this->hasMany('App\GiftVoucher', 'gift_id')->where("status" , true);
    }

    public function isExpired()
    {
        return $this->end_at ? Carbon::now()->gte($this->end_at) : false;
    }
}