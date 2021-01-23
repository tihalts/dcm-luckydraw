<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ScratchCardRequest extends Model
{
    protected $table = 'scratch_card_requests';
    public $timestamps = true;

    protected $fillable = [
        'campaign_id',
        'customer_id',
        'token',
        'scratch_cards',
        'accepted_at',
        'status'
    ];

    protected $dates = [
        'accepted_at'
    ];

    public function Campaign()
    {
        return $this->belongsTo('App\Campaign' , 'campaign_id');
    }

    public function Customer()
    {
        return $this->belongsTo('App\Customer' , 'customer_id');
    }
}