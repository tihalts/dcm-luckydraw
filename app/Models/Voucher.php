<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Voucher extends Model
{
    use LogsActivity;
    
    protected $table = 'vouchers';
    public $timestamps = true;

    protected $fillable = [
        'code',
        'customer_id',
        'campaign_id',
        'user_id',
        'voucher_amount',
        'redeemed_at',
        'expires_at',
        'status'
    ];

    protected static $logAttributes = ['code' , 'voucher_amount', 'status'];

    protected static $logName = 'voucher';

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;

    public function getDescriptionForEvent(string $eventName): string
    {
        return "This voucher has been {$eventName}";
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

    public function isExpired()
    {
        return $this->expires_at ? Carbon::now()->gte($this->expires_at) : false;
    }
}