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

class ShopController extends Controller 
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
            $shops = App\Shop::where('status' , true);
            $this->response['data'] = $shops->with(['BusinessType'])->offset($this->filter["itemPerPage"] * ($page - 1))
                                                ->limit($this->filter["itemPerPage"])
                                                ->get();
            $this->response["totalItems"] = $shops->count();
            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
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
            $shops = App\Shop::where('status' , true);

            if(isset($filter['searchText'])){
                $shops = $shops->where(function($query) use($filter){
                                    $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                          ->orWhere('shop_no' , 'LIKE' , '%'.$filter['searchText'].'%');
                                });
            }   
             
            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $shops = $shops->whereDate('created_at' , '>=' , $start_date)
                                ->whereDate('created_at' , '<=' , $end_date);
            }

            if(isset($filter['business_type_id'])){
                $shops = $shops->where('business_type_id',  $filter["business_type_id"]);
            }

            $this->response["totalItems"] = $shops->count();

            if(isset($filter['filter_by'])){
                $shops = $shops->orderBy($filter['filter_by'] , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"]);
            } else{
                $shops = $shops->orderBy('id' , 'desc');
            }

            

            $this->response['data'] = $shops->with(['BusinessType'])->offset(isset($filter["itemPerPage"]) ? $filter["itemPerPage"] * ($page - 1) : $this->filter["itemPerPage"] * ($page - 1))
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
            'shop_number' => 'required|max:255|unique:shops,shop_no',
            'shop_name' => 'required|max:255'
        ]);
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {

                $shop = new App\Shop();
                $shop->shop_no = $request->input('shop_number');
                $shop->name = $request->input('shop_name');
                $shop->description = $request->has('description') ? $request->input('description') : null;
                $shop->business_type_id = $request->input('business_type_id');
                $shop->save();

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $shop;
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
            $shop = App\Shop::select('id' , 'shop_no as shop_number' , 'name as shop_name' , 'description' , 'business_type_id')
                                ->where('id' , $id)
                                ->first();

            $this->response['data'] = $shop;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function shop_details($id)
    {
        try {            
            $shop = App\Shop::select('id' , 'shop_no as shop_number' , 'name as shop_name' , 'description' , 'business_type_id')
                                ->where('id' , $id)
                                ->with('Category')
                                ->first();

            if(isset($shop)){
                $shop->total_sales =  App\Purchase::where('shop_id' , $id)->where('status' , 1)->sum('amount');
            }

            $this->response['data'] = $shop;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function update(Request $request , $id)
    {
        $validator = Validator::make($request->all(), [
            'shop_number' => 'required|max:255|unique:shops,shop_no,' .$id,
            'shop_name' => 'required|max:255'
        ]);
        try {
            $this->response["title"] = Lang::get('');
    
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {

                $shop = App\Shop::find($id);
                $shop->shop_no = $request->input('shop_number');
                $shop->name = $request->input('shop_name');
                $shop->description = $request->has('description') ? $request->input('description') : $shop->description;
                $shop->business_type_id = $request->has('business_type_id') ? $request->input('business_type_id') : $shop->business_type_id;
                $shop->save();

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $shop;
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

    public function getBusinessTypes(Request $request)
    {
        try {   
            $filter = $request->all(); 
            $business_types = new App\BusinessType();
            if(isset($filter['searchText'])){
                $business_types = $business_types->where(function($query) use($filter){
                    $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
                });
            }
            $this->response["data"] = $business_types->get();
            return $this->successResponse($this->response);
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function getShops(Request $request)
    {
        try {      
            $filter = $request->all(); 
            $shops = App\Shop::where('status' , true);

            if(isset($filter['category_id'])){
                $shops = $shops->where('business_type_id' , $filter['category_id']);
            }

            if(isset($filter['searchText'])){
                $shops = $shops->where(function($query) use($filter){
                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                      ->orWhere('shop_no' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }  
            $this->response['data'] = $shops->with(['BusinessType'])->get();
           
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function create_shops()
    {
        $settings = config('shops');
        $categories = $settings['category_list'];
        $shops = $settings['shop_list'];
        foreach ($categories as $key => $category) {
            App\BusinessType::updateOrcreate([
               "name" => $category['category']
            ],[
                "description" => $category['category'],
                "status" => true
            ]);
        }
        foreach ($shops as $key => $shop) {
            $category = App\BusinessType::where('name' , $shop['category'])->first();
            if(!isset($category)) continue;
            App\Shop::updateOrcreate([
               "shop_no" => $shop['number']
            ],[
                "name" => $shop['name'],
                "description" => $shop['description'],
                "business_type_id" => $category->id,
                "status" => true
            ]);
        }
        return "success";
    }
}