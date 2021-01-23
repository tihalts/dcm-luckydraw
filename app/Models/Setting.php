<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model 
{

    protected $table = 'settings';
    public $timestamps = true;

    protected $fillable = ['key', 'value', 'user_id' , 'status'];

}