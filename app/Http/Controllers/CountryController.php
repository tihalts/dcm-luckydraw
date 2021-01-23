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


class CountryController extends Controller 
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
            $users = App\Country::where('status' , true);   
            $this->response['data'] = $users->latest()
                                            ->offset($this->filter["itemPerPage"] * ($page - 1))
                                            ->limit($this->filter["itemPerPage"])
                                            ->get();

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response["totalItems"] = $users->count();
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
            $user = App\Country::where("role" , 'admin')->where('status' , true);

            if(isset($filter['searchText'])){
                $user = $user->where(function($query) use($filter){
                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                        ->orWhere('short_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                        ->orWhere('iso' , 'LIKE' , '%'.$filter['searchText'].'%')
                                        ->orWhere('phone_code' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }    

            $this->response['data'] = $user->orderBy('id' , $filter["orderby"] ? $filter["orderby"] : $this->filter["orderby"])
                                            ->offset($filter["itemPerPage"] ? $filter["itemPerPage"] * ($page - 1) : $this->filter["itemPerPage"] * ($page - 1))
                                            ->limit($filter["itemPerPage"] ? $filter["itemPerPage"] : $this->filter["itemPerPage"])
                                            ->get();
            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response["totalItems"] = $user->count();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'mobile'  => 'required|max:10|min:10|unique:users,mobile',
            'country_iso' => 'required|exists:countries,iso'
        ]);

       
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $country = App\Country::where('iso' , $request->input('country_iso'))->first();
                $user = new App\Country();
                $user->first_name = $request->input('first_name');
                $user->last_name = $request->input('last_name');
                $user->email = $request->input('email');
                $user->mobile = $request->input('mobile');
                $user->password = Hash::make(str_random(8));
                $user->country_iso = $request->input('country_iso');
                $user->phone_code = $country->phone_code;
                $user->save();

                //$file = $user->uploadProfileImage($request , 'profile_image' , 'user/profile');

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $user;
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
            $user = App\Country::find($id);
            $this->response["data"] = $user;
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function update(Request $request , $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,' .$id,
            'mobile' => 'required|max:10|min:10|unique:users,mobile,'.$id,
            'country_iso' => 'required|exists:countries,iso'
        ]);
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $country = App\Country::where('iso' , $request->input('country_iso'))->first();
                $user = App\Country::find($id);
                $user->first_name = $request->input('first_name');
                $user->last_name = $request->input('last_name');
                $user->email = $request->input('email');
                $user->mobile = $request->input('mobile');
                $user->country_iso = $request->input('country_iso');
                $user->phone_code = $country->phone_code;
                $user->save();

                //$file = $user->uploadProfileImage($request , 'profileImage' , 'user/profile');

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $user;
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