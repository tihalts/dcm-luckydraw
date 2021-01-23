<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CampaignTemplate extends Model
{
    protected $table = 'campaign_templates';
    public $timestamps = true;

    protected $fillable = [
        'campaign_id',
        'email',
        'sms',
        'status'
    ];
}