<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model 
{

    protected $table = 'customers';
    public $timestamps = true;

    protected $fillable = [
        'first_name',
        'last_name',
        'cpr',
        'email' , 
        'phone_code',
        'mobile' ,
        'password' ,
        'country_iso',
        'nationality',
        'created_user_id',
        'created_at',
        'updated_at',
        'status' 
    ];
    
    protected $appends = ["fullname" , "points" , "purchase_amount" ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getFullNameAttribute() 
    {
        return ucfirst(strtolower($this->first_name)) .' ' . ucfirst(strtolower($this->last_name));
    }  

    public function Winner()
    {
      return $this->hasOne('App\Winner' , 'customer_id');
    }

    public function Purchases()
    {
      return $this->hasMany('App\Purchase' , 'customer_id')->where('status' , true);
    }

    public function getPointsAttribute() 
    {
        return (int) $this->Points()->count();
    } 

    public function getPurchaseAmountAttribute() 
    {
        return (int) $this->Purchases()->sum('amount');
    } 

    public function getNationalityAttribute($value) 
    {
        if(isset($value)){
            return Country::where('iso' , $value)->select('id' , 'name' , 'iso')->first();
        }
        return json_decode ("{}");;
    } 

    public function ScratchCards()
    {
        return $this->hasMany('App\ScratchCard' , 'customer_id');
    }

    public function ScratchCardWinners()
    {
        return $this->hasMany('App\ScratchCard' , 'customer_id')->where('is_winner' , true);
    }

    public function SpinWinners()
    {
        return $this->hasMany('App\SpinWinner' , 'customer_id');//->where('is_winner' , true);
    }

    public function Vouchers()
    {
        return $this->hasMany('App\Voucher' , 'customer_id');
    }

    public function FreeGifts()
    {
        return $this->hasMany('App\GiftVoucher' , 'customer_id');
    }

    public function Points()
    {
        return $this->hasMany('App\LuckyDrawPoint' , 'customer_id');
    }

    public function CreatedBy()
    {
        return $this->belongsTo('App\User' , 'created_user_id')->select('id' , 'first_name', 'last_name' , 'email', 'mobile');
    }
}