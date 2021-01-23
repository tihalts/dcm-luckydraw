<?php

namespace App;

use App\Traits\HasPurchaseActionTrait;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, LogsActivity , HasPurchaseActionTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email' , 
        'phone_code',
        'mobile' ,
        'password' ,
        'country_iso',
        'role',
        'status' 
    ];

    protected static $logAttributes = ['first_name', 'last_name' , 'email' , 'mobile' , 'role'];

    protected static $logName = 'user';

    protected static $logOnlyDirty = true;

    protected static $submitEmptyLogs = false;

    public function getDescriptionForEvent(string $eventName): string
    {
        return "This user has been {$eventName}";
    }


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ["fullname" , 'avatar'];

    public function getFullNameAttribute() 
    {
        return ucfirst(strtolower($this->first_name)) .' ' . ucfirst(strtolower($this->last_name));
    }  

    public function getAvatarAttribute() 
    {
        $file = ProfileImage::where('model_type' , get_class($this))->where("model_id", $this->id)->where("status" , true)->first();
        return isset($file) ? url(Storage::url($file->path)) : "img/user.png";
    } 
    
    public function Vouchers()
    {
        return $this->hasMany('App\Voucher' , 'user_id');
    }

    public function ScratchCards()
    {
        return $this->hasMany('App\ScratchCard' , 'user_id');
    }
}
