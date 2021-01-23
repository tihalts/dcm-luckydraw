<?php

namespace App\Libraries;

use App;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Request;

class Permission {    

    public function __construct(){ 
        //
    }

    public function UpdatePermissionInfo(){
        try{
            $permissions = include(app_path() . "/Constants/Permission.php");             
            foreach ($permissions as  $module => $menus) {  
                foreach ($menus as $menu_key => $operations) {
                    App\Permission::updateOrCreate(["name" => snake_case($menu_key)],
                                                   ["status" => 1 ,"description" => $module]);
                    foreach ($operations as $operation) {                    
                        App\Permission::updateOrCreate(["name" => snake_case($operation . '_'.$menu_key)],
                                                            ["status" => 1 ,"description" => $menu_key]);
                    }
                } 
            }
            return 1;
        }
        catch (Exception $e){
            return $e;
        }
    }

    public function SamplePermission(){        
        try{
            $lists = collect(App\Permission::get())->pluck("id");
            foreach ($lists as $list) {
                $permission = new App\RolePermission();
                $permission->permission_id = $list;
                $permission->is_allow = true;
                $permission->permission_for = "role";
                $permission->permission_for_id = 1;
                $permission->project_id = 1;
                $permission->status = 1;
                $permission->save();
            }
            return $lists;
        }
        catch (Exception $e){
            return $e;
        }
    }


    public function Can($name = null){
        if(is_null($name)) return 0;
        return true;
        // $permission = App\Permission::where('name' , $name )->where('status' , 1)->first();
        // if(!isset($permission->id) ) return 0;
        // return App\RolePermission::where('permission_for' , 'role')
        //                             ->whereIn('permission_for_id' , Permission::UserRoleIds())
        //                             ->where('permission_id' , $permission->id)
        //                             ->where('status' , 1)
        //                             ->where('is_allow', 1)
        //                             ->count() == 0 ? 0 : 1;
    }

    public function UserSelectedProject(){
        // if(!session()->has('selected_project')){            
        //     session(['selected_project' => Setting::UserDefaultProject()]);
        // }  
        return Setting::UserDefaultProject();
    }

    public function isUserSelectedProject(){
        return session()->has('selected_project') ? true : false;
    }

    public function UserSelectedCompany(){
        // if(!session()->has('selected_company')){
        //     session(['selected_company' => Setting::UserDefaultCompany()]);
        // }
        return Setting::UserDefaultCompany();
    }     

    public function CurrentCompanyProjects(){
        return App\Project::where('project_company_id', $this->UserSelectedCompany())->where('status',1)->get()->pluck("id");
    }

    public function isUserSelectedCompany(){
        return session()->has('selected_company') ? true : false;
    } 

    public function CompanyProjects($project_id = null){
        if(isset($project_id)){
            return App\Project::where('project_company_id', $project_id)->where('status',1)->get()->pluck("id");
        }else{
            return App\Project::where('project_company_id', $this->UserSelectedCompany())->where('status',1)->get()->pluck("id");
        }        
    }

    public function UserRoleIds(){    
        return Auth::user()->UserRoleIds($this->UserSelectedProject());
    }

    public function UserRoleNames(){
       return Auth::user()->UserRoleNames($this->UserRoleIds());
    }

    public function RoleId($name = ""){
        if($role = App\Role::where("role_key" , $name)->first())
           return $role->id;
        else
           return 0;
    }

    public function UserGroupIds($project_id = null){
        if(isset($project_id)){
            return App\CrewGroupUser::where('project_id' , $project_id)->where("group_user_id" , Auth::id())->where("status" , 1)->get()->pluck("group_id");
        }else{
            return App\CrewGroupUser::where('project_id' , $this->UserSelectedProject())->where("group_user_id" , Auth::id())->where("status" , 1)->get()->pluck("group_id");
        }
        
    } 

    public function UserCompanyAdminIds(){
      return App\Company::where('company_user_id', Auth::id())->where('status',1)->get()->pluck("id");
    }

    public function UserAdminProjectIds(){
        return App\Project::where('project_user_id', Auth::id())->where('status',1)->get()->pluck("id");
    }
    public function ProjectCount(){
        return App\Project::where('project_user_id', Auth::id())->where('status',1)->count();
    }
    public function UserProjectIds($user_id)
    {
        if(isset($user_id)){
             return  collect(App\UserRole::where("user_id" , $user_id)->where("status" , 1)->get())->pluck("project_id");
        }else{
            return [];
        }
    }

    public function UserCompanyIds($user_id)
    {
        if(isset($user_id)){
             return  collect(App\Project::whereIn("id" , $this->UserProjectIds($user_id))->where("status" , 1)->get())->pluck("project_company_id");
        }else{
            return [];
        }
    }
}
