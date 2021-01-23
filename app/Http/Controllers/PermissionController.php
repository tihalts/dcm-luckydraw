<?php 

namespace App\Http\Controllers;

use App;
use Lang;
use Carbon\Carbon;
use App\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller 
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
            $this->response['data'] = App\Permission::get();
            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response["totalItems"] = App\Permission::count();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function search(Request $request , $page = 1)
    {
        try {            
            $this->response['data'] = Permission::get();
            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response["totalItems"] = Permission::count();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'permission_name' => 'required|max:255'
        ]);
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $permission = new App\Permission();
                $permission->name = $request->input('permission_name');
                $permission->slug = str_slug($request->input('permission_name') , '_');
                $permission->description = $request->has('description') ? $request->input('description') : $role->description ;
                $permission->type = $request->has('type_name') ?  $request->input('type_name') : 'web';
                $permission->group = $request->input('group_name');
                $permission->save();
                $this->response["message"] = Lang::get('');
                $this->response["data"] = $permission;
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
            $this->response['data'] = Permission::select('id' , 'name as permission_name' , 'type as type_name','description','group')->where('id' , $id)->first();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function update(Request $request , $id)
    {
        $validator = Validator::make($request->all(), [
            'permission_name' => 'required|max:255'
        ]);
        try {
            $this->response["title"] = Lang::get('');
    
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $permission = Permission::find($id);
                $permission->name = $request->input('permission_name');
                $permission->slug = str_slug($request->input('permission_name') , '_');
                $permission->type = $request->has('type_name') ?  $request->input('type_name') : 'web';
                $permission->group = $request->input('group');
                $permission->save();
                $this->response["message"] = Lang::get('');
                $this->response["data"] = $permission;
                return $this->successResponse($this->response);
            }
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }
    
    public function destroy($id)
    {

    }
  
}