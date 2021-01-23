<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model 
{

    protected $table = 'site_settings';
    public $timestamps = true;

    protected $fillable = ['key', 'value', 'status'];

}