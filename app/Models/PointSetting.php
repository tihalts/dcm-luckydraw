<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PointSetting extends Model 
{

    protected $table = 'point_settings';
    public $timestamps = true;

    protected $fillable = [
        'purchase_from' , 
        'purchase_to',
        'purchase_points' ,
        'status' 
    ]; 
}