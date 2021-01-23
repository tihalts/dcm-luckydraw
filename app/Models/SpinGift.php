<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SpinGift extends Model
{
    protected $table = 'spin_gifts';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'code',
        'description',
        'image',
        'no_of_gifts',
        'start_at',
        'end_at',
        'spinner_id',
        'status'
    ];

    // protected $dates = [
    //     'start_at' , 'end_at'
    // ];

    public function Spinner()
    {
        return $this->belongsTo('App\Spinner', 'spinner_id');
    }

    public function Items()
    {
        return $this->hasMany('App\SpinGiftItem', 'gift_id')->where("status" , true);
    }

    public function isExpired()
    {
        return $this->end_at ? Carbon::now()->gte($this->end_at) : false;
    }
}