<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Winner extends Model 
{

    protected $table = 'winners';
    public $timestamps = true;

    protected $fillable = [
        'uuid' ,
        'lucky_draw_id',
        'customer_id',
        'position',
        'status'
    ]; 

    public function Customer()
    {
        return $this->belongsTo('App\Customer' , 'customer_id');
    }

    public function LuckyDraw()
    {
        return $this->belongsTo('App\LuckyDraw' , 'lucky_draw_id');
    }
}