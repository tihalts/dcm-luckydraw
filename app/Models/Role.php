<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model 
{

   protected $table = 'roles';
   public $timestamps = true;
   protected $fillable = ['name', 'slug', 'description' , 'type', 'group' , 'status'];  
    
   public function Permissions(){
      return $this->belongsToMany('App\Permission', 'role_permissions' ,  'role_id' , 'permission_id')
                  ->withPivot('id')
                  ->where('role_permissions.status'  , true);
   }

   public function isAllow($permission_id){
         return $this->Permissions()->where('role_permissions.permission_id' , $permission_id)->count() == 0 ? 0 : 1;
   }
    
   public function getPermissions(){
      $permissions = Permission::where('status' , true)->where('type' , $this->type)->get();
      $toArray = [];
      foreach ($permissions as  $permission) {
         $toArray[$permission->group][] = [
            'id' => $permission->id ,
            'name' => $permission->name ,
            'slug' => $permission->slug,
            'is_allow' => $this->isAllow($permission->id)
         ];
      }
      return $toArray;
   }

}