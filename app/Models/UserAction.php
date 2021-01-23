<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserAction extends Model 
{

    protected $table = 'user_actions';
    public $timestamps = true;

    protected $primaryKey = "user_id";
    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'type',
        'data' , 
        'customer_id',
        'status' 
    ]; 

    protected $casts = [
        'data' => 'json',
    ];
}