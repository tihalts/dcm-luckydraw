<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CustomerVoucher extends Model
{
    protected $table = 'customer_vouchers';
    public $timestamps = true;
    
    protected $fillable = [
        'customer_id',
        'voucher_id',
        'redeemed_at',
        'status'
    ];

    protected $dates = [
        'expires_at'
    ];

    protected $casts = [
        'data' => 'collection'
    ];

    public function customers()
    {
        return $this->belongsToMany('App\Customervoucher', 'voucher_id')->withPivot('redeemed_at');
    }

    public function isExpired()
    {
        return $this->expires_at ? Carbon::now()->gte($this->expires_at) : false;
    }
}