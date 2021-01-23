<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class SpinWinner extends Model
{
    use LogsActivity;
    
    protected $table = 'spin_winners';
    public $timestamps = true;

    protected $fillable = [
        'code',
        'spinner_id',
        'customer_id',
        'user_id',
        'gift_id',
        'is_winner',
        'spin_at',
        'is_gifted',
        'expires_at',
        'status'
    ];

    protected static $logAttributes = ['code' , 'spin_at', 'status'];

    protected static $logName = 'Spin & Win';

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;

    public function getDescriptionForEvent(string $eventName): string
    {
        return "This spainner has been {$eventName}";
    }


    // protected $dates = [
    //     'scratched_at'
    // ];

    public function Spinner()
    {
        return $this->belongsTo('App\Spinner' , 'spinner_id');
    }

    public function Gift()
    {
        return $this->belongsTo('App\SpinGift' , 'gift_id');
    }

    public function GiftCode()
    {
        return $this->belongsTo('App\SpinGiftItem' , 'code');
    }

    public function Customer()
    {
        return $this->belongsTo('App\Customer' , 'customer_id');
    }

    public function Provider()
    {
        return $this->belongsTo('App\User' , 'user_id');
    }
}