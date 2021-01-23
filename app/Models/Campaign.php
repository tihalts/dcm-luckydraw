<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model 
{

    protected $table = 'campaigns';
    public $timestamps = true;

    protected $casts = [
        'data' => 'array',
    ];

    protected $fillable = [
        'name',
        'description' ,
        'data' ,
        'campaign_type',
        'start_at',
        'end_at',
        'send_sms',
        'send_email',
        'status' 
    ]; 

    // protected $dates = [
    //     'start_at' , 'end_at'
    // ];

    public function Gifts()
    {
        return $this->hasMany('App\Gift', 'campaign_id');
    }

    public function Group()
    {
        return $this->belongsTo('App\CampaignGroup', 'group_id');
    }

    public function Winners()
    {
       return $this->hasMany('App\ScratchCard' , 'campaign_id')->where('is_winner' , true);
    }

    public function Vouchers()
    {
       return $this->hasMany('App\Voucher' , 'campaign_id')->where('status' , true);
    }

    public function GiftVouchers()
    {
       return $this->hasMany('App\GiftVoucher' , 'campaign_id')->where('status' , true);
    }

    public function isExpired()
    {
        return $this->end_at ? Carbon::now()->gte($this->end_at) : false;
    }
    
    public function gettotalGiftItemsAttribute($value)
    {
        $campaign_id = $this->id;
        return  GiftItem::where('status' , true)
                                ->whereHas('gift' , function($query) use($campaign_id) {
                                    $query->where("campaign_id" , $campaign_id)
                                        ->where("status" , 1);
                                })
                                ->count();
    }

    public function gettotalUnGiftItemsAttribute($value)
    {
        $campaign_id = $this->id;
        return GiftItem::where('status' , true)
                            ->whereHas('gift' , function($query) use($campaign_id) {
                                $query->where("campaign_id" , $campaign_id)
                                    ->where("status" , 1);
                            })
                            ->whereNull('gifted_at')
                            ->count();
    }

    public function gettodayGiftItemsAttribute($value)
    {
        $campaign_id = $this->id;
        return GiftItem::where('status' , true)
                            ->whereHas( 'gift' , function($query) use($campaign_id) {
                                $query->where("campaign_id" , $campaign_id)
                                    ->where("status" , 1);
                            })
                            ->whereNull('gifted_at')
                            ->whereDate('gift_at' , Carbon::now())
                            ->count();
    }

    public function gettodayGiftedItemsAttribute($value)
    {
        $campaign_id = $this->id;
        return GiftItem::where('status' , true)
                            ->whereDate('gift_at' , Carbon::now())
                            ->whereHas('gift' , function($query) use($campaign_id) {
                                $query->where("campaign_id" , $campaign_id)
                                    ->where("status" , 1);
                            })
                            ->whereNotNull('gifted_at')
                            ->count();
    }

    public function getyesterdayGiftItemsAttribute($value)
    {
        $campaign_id = $this->id;
        return GiftItem::where('status' , true)
                            ->whereHas( 'gift' , function($query) use($campaign_id) {
                                $query->where("campaign_id" , $campaign_id)
                                    ->where("status" , 1);
                            })
                            ->whereNull('gifted_at')
                            ->whereDate('gift_at' , Carbon::now()->subDays(1))
                            ->count();
    }

    public function getyesterdayGiftedItemsAttribute($value)
    {
        $campaign_id = $this->id;
        return GiftItem::where('status' , true)
                            ->whereDate('gift_at' , Carbon::now()->subDays(1))
                            ->whereHas('gift' , function($query) use($campaign_id) {
                                $query->where("campaign_id" , $campaign_id)
                                    ->where("status" , 1);
                            })
                            ->whereNotNull('gifted_at')
                            ->count();
    }
}