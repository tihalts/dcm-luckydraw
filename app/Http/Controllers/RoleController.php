<?php 

namespace App\Http\Controllers;

use App;
use Lang;
use Config;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller 
{

    var $response = [];
    var $filter = [
        "searchText" => "",
        "itemPerPage" => 15
    ];

    public function __construct()
    {        
        
    }
    public function index()
    {

    }

    public function list($page = 1)
    {     
        try {            
            $this->response['data'] = App\Role::get();
            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response["totalItems"] = App\Role::count();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function search(Request $request , $page = 1)
    {
        try {            
            $this->response['data'] = Role::get();
            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response["totalItems"] = Role::count();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|max:255'
        ]);
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $role = new App\Role();
                $role->name = $request->input('role_name');
                $role->description = $request->has('description') ? $request->input('description') : "";
                $role->slug = str_slug($request->input('role_name') , '_');
                $role->group = $request->input('group_name');
                $role->type = $request->has('type_name') ?  $request->input('type_name') : 'web';
                $role->save();
                $this->response["message"] = Lang::get('');
                $this->response["data"] = $role;
                return $this->successResponse($this->response);
            }
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }

    }

    public function show($id)
    {

    }

    public function fetch($id)
    {
        try {            
            $this->response['data'] = App\Role::select('id' , 'name as role_name' , 'type as type_name' , 'group as group_name' , 'description')->where('id' , $id)->first();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function update(Request $request , $id)
    {
        $validator = Validator::make($request->all(), [
            'role_name' => 'required|max:255'
        ]);
        try {
            $this->response["title"] = Lang::get('');
    
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $role = App\Role::find($id);
                $role->name = $request->input('role_name');
                $role->slug = str_slug($request->input('role_name') , '_');
                $role->description = $request->has('description') ? $request->input('description') : $role->description ;
                $role->group = $request->input('group_name');
                $role->type = $request->has('type_name') ?  $request->input('type_name') : $role->type_name;
                $role->save();
                $this->response["message"] = Lang::get('');
                $this->response["data"] = $role;
                return $this->successResponse($this->response);
            }
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function update_permissions(Request $request, $role_id)
    {
        try { 
            $role = App\Role::find($role_id);
            $group_permissions = $request->has('permissions') ? $request->input('permissions') : [];
            App\RolePermission::where('role_id' , $role_id)->update(['status' => false]);
            foreach($group_permissions as $key => $permissions){
                foreach($permissions as $permission){
                    App\RolePermission::updateOrCreate(
                        ['permission_id' => $permission['id'] ,'role_id' => $role->id],
                        ['status' => $permission['is_allow']]
                    );
                }                
            }
            $role->permissions = $role->getPermissions();
            $this->response["title"] = Lang::get('Role permission update');
            $this->response["message"] = Lang::get('Role permission update successfull.');
            $this->response["data"] = $role ? $role : [];
            return $this->successResponse($this->response);
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function permissions($id){
        try { 
            $role = App\Role::where('id' , $id)->where('status' , true)->first();
            if(isset($role))
               $role->permissions = $role->getPermissions();
            
            $this->response["title"] = Lang::get('');
            $this->response["message"] = Lang::get('');
            $this->response["data"] = $role ? $role : []; 
            return $this->successResponse($this->response);
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }
    
    public function destroy($id)
    {

    }

    public function getRoles(){
        return App\Role::where('status' , true)->get();
    }

    public function setup()
    {
        try { 
            $roles = Config::get('rolepermission.roles');

            foreach ($roles as $key => $role) {
                App\Role::updateOrCreate([
                    'slug' => $role['slug']
                ],[
                    "name" => $role['name'],
                    "description" => $role['description'],
                    "type" => $role['type'],
                    "group" => $role['group'],
                ]);
            }

            $permissions = Config::get('rolepermission.permissions');

            foreach ($permissions as $key => $permission) {
                App\Permission::updateOrCreate([
                    'slug' => $permission['slug']
                ],[
                    "name" => $permission['name'],
                    "description" => $permission['description'],
                    "type" => $permission['type'],
                    "group" => $permission['group'],
                ]);
            }
            
            $this->response["title"] = Lang::get('Role and Permission setup');
            $this->response["message"] = Lang::get('Role and Permission setup successfull.');
            $this->response["data"] = []; 
            return $this->successResponse($this->response);
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }
  
}