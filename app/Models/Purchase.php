<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Purchase extends Model 
{
    use LogsActivity;

    protected $table = 'purchases';
    public $timestamps = true;

    protected $fillable = [
        'purchase_no',
        'amount', 
        'points',
        'customer_id',
        'user_id',
        'shop_id',
        'created_at',
        'updated_at',
        'status' 
    ]; 

    protected static $logAttributes = ['id' , 'purchase_no', 'amount' , 'points' , 'customer_id' , 'status'];

    protected static $logName = 'purchase';

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;

    public function getDescriptionForEvent(string $eventName): string
    {
        return "This purchase has been {$eventName}";
    }

    public function Customer()
    {
        return $this->belongsTo('App\Customer' , 'customer_id');
    }

    public function User()
    {
        return $this->belongsTo('App\User' , 'user_id');
    }

    public function Shop()
    {
        return $this->belongsTo('App\Shop' , 'shop_id');
    }

    public function LuckyDrawPoints()
    {
        return $this->hasMany('App\LuckyDrawPoint' , 'purchase_id');
    }
}