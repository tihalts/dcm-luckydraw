<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model 
{

    protected $table = 'permissions';
    public $timestamps = true;
    protected $fillable = ['name', 'slug', 'description', 'type', 'group' , 'status'];

    public function RolePermissions()
    {
        return $this->hasMany('App\RolePermission' , 'permission_id');
    }
    
}