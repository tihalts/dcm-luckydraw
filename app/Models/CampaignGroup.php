<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class CampaignGroup extends Model 
{

    protected $table = 'campaign_groups';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'description' ,
        'start_at',
        'end_at',
        'status' 
    ]; 

    public function Campagins()
    {
        return $this->hasMany('App\Campagin', 'group_id');
    }

}