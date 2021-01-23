<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model 
{

    protected $table = 'shops';
    public $timestamps = true;

    protected $fillable = [
        'shop_no',
        'name',
        'description' , 
        'business_type_id' ,
        'active_status',
        'status' 
    ]; 

    public function Category(){
        return $this->belongsTo('App\BusinessType', 'business_type_id');
    }

    public function BusinessType(){
        return $this->belongsTo('App\BusinessType', 'business_type_id');
    }
}