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

class BusinessTypeController extends Controller 
{

    var $response = [];
    var $filter = [
        "searchText" => "",
        "itemPerPage" => 15,
        "orderby" => 'desc'
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
            $business_types = App\BusinessType::where('status' , true);
            $this->response['data'] = $business_types->offset($this->filter["itemPerPage"] * ($page - 1))
                                                ->limit($this->filter["itemPerPage"])
                                                ->get();
            $this->response["totalItems"] = $business_types->count();
            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response["totalItems"] = App\BusinessType::count();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function search(Request $request , $page = 1)
    {
        try {      
            $filter = $request->all(); 
            $business_types = App\BusinessType::where('status' , true);

            if(isset($filter['searchText'])){
                $business_types = $business_types->where(function($query) use($filter){
                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }
            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $business_types = $business_types->whereDate('created_at' , '>=' , $start_date)
                                                 ->whereDate('created_at' , '<=' , $end_date);
            }

            $this->response["totalItems"] = $business_types->count();

            if(isset($filter['filter_by'])){
                $business_types = $business_types->orderBy($filter['filter_by'] , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"]);
            } else{
                $business_types = $business_types->orderBy('id' , 'desc');
            }

            $this->response['data'] = $business_types->offset(isset($filter["itemPerPage"]) ? $filter["itemPerPage"] * ($page - 1) : $this->filter["itemPerPage"] * ($page - 1))
                                            ->limit(isset($filter["itemPerPage"]) ? $filter["itemPerPage"] : $this->filter["itemPerPage"])
                                            ->get();
            $this->response["currentPage"] = $page;
            $this->response["filter"] = $filter;
           
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255'
        ]);
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {

                $business_type = new App\BusinessType();
                $business_type->name = $request->input('name');
                $business_type->description = $request->has('description') ? $request->input('description') : null;
                $business_type->save();

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $business_type;
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
            $business_type = App\BusinessType::select('id' , 'name as name' , 'description' )
                                ->where('id' , $id)
                                ->first();

            $this->response['data'] = $business_type;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function update(Request $request , $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255'
        ]);
        try {
            $this->response["title"] = Lang::get('');
    
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {

                $business_type = App\BusinessType::find($id);
                $business_type->name = $request->input('name');
                $business_type->description = $request->has('description') ? $request->input('description') : $business_type->description;
                $business_type->save();

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $business_type;
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