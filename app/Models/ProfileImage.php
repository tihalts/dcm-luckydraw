<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProfileImage extends Model 
{

    protected $table = 'profile_images';
    public $timestamps = true;

    protected $fillable = ['name', 'original_name', 'path' , 'disk' , 'extension' , 'mime_type' , 'size' , 'model_type' , 'model_id' , 'status'];

}