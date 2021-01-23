<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class GiftVoucher extends Model
{
    use LogsActivity;
    
    protected $table = 'gift_vouchers';
    public $timestamps = true;

    protected $fillable = [
        'gift_id',
        'code',
        'customer_id',
        'campaign_id',
        'user_id',
        'is_gifted',
        'status'
    ];

    protected static $logAttributes = ['code' , 'status'];

    protected static $logName = 'Gift Vouchers';

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;

    public function getDescriptionForEvent(string $eventName): string
    {
        return "This gift voucher has been {$eventName}";
    }


    // protected $dates = [
    //     'expires_at'
    // ];

    public function Campaign()
    {
        return $this->belongsTo('App\Campaign', 'campaign_id');
    }

    public function Customer()
    {
        return $this->belongsTo('App\Customer', 'customer_id');
    }

    public function Provider()
    {
        return $this->belongsTo('App\User' , 'user_id');
    }

    public function Gift()
    {
        return $this->belongsTo('App\Gift', 'gift_id');
    }

    public function Item()
    {
        return $this->belongsTo('App\GiftItem', 'code' , 'code');
    }
}