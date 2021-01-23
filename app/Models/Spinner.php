<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Spinner extends Model 
{

    protected $table = 'spinners';
    public $timestamps = true;

    protected $casts = [
        'data' => 'array',
    ];

    protected $fillable = [
        'name',
        'description' ,
        'data' ,
        'start_at',
        'end_at',
        'sms',
        'email',
        'send_sms',
        'send_email',
        'status' 
    ]; 

    // protected $dates = [
    //     'start_at' , 'end_at'
    // ];

    public function Gifts()
    {
        return $this->hasMany('App\SpinGift', 'spinner_id')->where('status' , 1);
    }

    public function Items()
    {
        return $this->hasMany('App\SpinnerItem', 'spinner_id')->where('status' , 1);
    }

    public function Spins()
    {
       return $this->hasMany('App\SpinWinner' , 'spinner_id')->where('status' , 1);
    }

    public function Winners()
    {
       return $this->hasMany('App\SpinWinner' , 'spinner_id')->where('is_winner' , true);
    }

    public function isExpired()
    {
        return $this->end_at ? Carbon::now()->gte($this->end_at) : false;
    }
    
    public function gettotalGiftItemsAttribute($value)
    {
        $spinner_id = $this->id;
        return  SpinGiftItem::where('status' , true)
                                ->whereHas('gift' , function($query) use($spinner_id) {
                                    $query->where("spinner_id" , $spinner_id)
                                        ->where("status" , 1);
                                })
                                ->count();
    }

    public function gettotalUnGiftItemsAttribute($value)
    {
        $spinner_id = $this->id;
        return SpinGiftItem::where('status' , true)
                            ->whereHas('gift' , function($query) use($spinner_id) {
                                $query->where("spinner_id" , $spinner_id)
                                    ->where("status" , 1);
                            })
                            ->whereNull('gifted_at')
                            ->count();
    }

    public function gettodayGiftItemsAttribute($value)
    {
        $spinner_id = $this->id;
        return SpinGiftItem::where('status' , true)
                            ->whereHas( 'gift' , function($query) use($spinner_id) {
                                $query->where("spinner_id" , $spinner_id)
                                    ->where("status" , 1);
                            })
                            ->whereNull('gifted_at')
                            ->whereDate('gift_at' , Carbon::now())
                            ->count();
    }

    public function gettodayGiftedItemsAttribute($value)
    {
        $spinner_id = $this->id;
        return SpinGiftItem::where('status' , true)
                            ->whereDate('gift_at' , Carbon::now())
                            ->whereHas('gift' , function($query) use($spinner_id) {
                                $query->where("spinner_id" , $spinner_id)
                                    ->where("status" , 1);
                            })
                            ->whereNotNull('gifted_at')
                            ->count();
    }

    public function getyesterdayGiftItemsAttribute($value)
    {
        $spinner_id = $this->id;
        return SpinGiftItem::where('status' , true)
                            ->whereHas( 'gift' , function($query) use($spinner_id) {
                                $query->where("spinner_id" , $spinner_id)
                                    ->where("status" , 1);
                            })
                            ->whereNull('gifted_at')
                            ->whereDate('gift_at' , Carbon::now()->subDays(1))
                            ->count();
    }

    public function getyesterdayGiftedItemsAttribute($value)
    {
        $spinner_id = $this->id;
        return SpinGiftItem::where('status' , true)
                            ->whereDate('gift_at' , Carbon::now()->subDays(1))
                            ->whereHas('gift' , function($query) use($spinner_id) {
                                $query->where("spinner_id" , $spinner_id)
                                    ->where("status" , 1);
                            })
                            ->whereNotNull('gifted_at')
                            ->count();
    }
}