<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model 
{

    protected $table = 'role_permissions';
    public $timestamps = true;

    protected $fillable = ['role_id', 'permission_id' , 'status'];

}