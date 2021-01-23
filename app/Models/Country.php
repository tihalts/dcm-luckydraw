<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Country extends Model 
{

    protected $table = 'countries';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'short_name',
        'iso',
        'iso3' , 
        'numcode',
        'phone_code',
        'status' 
    ]; 

    public function Customers()
    {
        return $this->belongsTo('App\Customer', 'iso' , 'country_iso')->where("status" , true);
    }
}