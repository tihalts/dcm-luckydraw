<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SpinnerItem extends Model
{    
    protected $table = 'spinner_items';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'bg_color',
        'text_color',
        'gift_id',
        'spinner_id',
        'status'
    ];

    // protected $dates = [
    //     'scratched_at'
    // ];

    public function Spinner()
    {
        return $this->belongsTo('App\Spinner' , 'spinner_id');
    }

    public function Gift()
    {
        return $this->belongsTo('App\SpinGift' , 'gift_id');
    }
}