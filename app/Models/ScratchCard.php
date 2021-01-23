<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class ScratchCard extends Model
{
    use LogsActivity;
    
    protected $table = 'scratch_cards';
    public $timestamps = true;

    protected $fillable = [
        'code',
        'campaign_id',
        'customer_id',
        'user_id',
        'gift_id',
        'is_winner',
        'scratched_at',
        'is_gifted',
        'expires_at',
        'created_at',
        'updated_at',
        'status'
    ];

    protected static $logAttributes = ['code' , 'scratched_at', 'status'];

    protected static $logName = 'Scratch Card';

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;

    public function getDescriptionForEvent(string $eventName): string
    {
        return "This scratch card has been {$eventName}";
    }


    // protected $dates = [
    //     'scratched_at'
    // ];

    public function Campaign()
    {
        return $this->belongsTo('App\Campaign' , 'campaign_id');
    }

    public function Gift()
    {
        return $this->belongsTo('App\Gift' , 'gift_id');
    }

    public function GiftCode()
    {
        return $this->belongsTo('App\GiftItem' , 'code');
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