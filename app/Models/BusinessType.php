<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BusinessType extends Model
{
    protected $table = 'business_types';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'status'
    ];

    protected $dates = [];
}