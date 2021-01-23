<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class LuckyDrawPoint extends Model 
{

    protected $table = 'lucky_draw_points';
    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'purchase_id',
        'customer_id',
        'status' 
    ]; 

    public function Winners()
    {
        return $this->hasMany('App\Winner' , 'uuid' , 'uuid')->where('status' , 1);
    }
}