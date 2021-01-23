<?php 

namespace App\Http\Controllers;

use App;
use Lang;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class PurchasePointController extends Controller 
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
            $this->response['data'] = App\PointSetting::where('status' , true)->get();
            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response["totalItems"] = App\PointSetting::where('status' , true)->count();
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
            $point = App\PointSetting::where('status' , true);

            if(isset($filter['searchText'])){
                $point = $point->where('first_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                             ->orWhere('last_name' , 'LIKE' , '%'.$filter['searchText'].'%');
            }    

            $this->response['data'] = $point->get();
            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response["totalItems"] = $point->count();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_from' => 'required|numeric',
            'purchase_to' => 'required|numeric',
            'purchase_points' => 'required|numeric',
        ]);

       
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $point = new App\PointSetting();
                $point->purchase_from = $request->input('purchase_from');
                $point->purchase_to = $request->input('purchase_to');
                $point->purchase_points = $request->input('purchase_points');
                $point->save();

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $point;
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
        try{
            $point = App\PointSetting::find($id);
            $this->response["data"] = $point;
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function update(Request $request , $id)
    {
        $validator = Validator::make($request->all(), [
            'purchase_from' => 'required|numeric',
            'purchase_to' => 'required|numeric',
            'purchase_points' => 'required|numeric',
        ]);
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $point = App\PointSetting::find($id);
                $point->purchase_from = $request->input('purchase_from');
                $point->purchase_to = $request->input('purchase_to');
                $point->purchase_points = $request->input('purchase_points');
                $point->save();

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $point;
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