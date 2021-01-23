<?php 

namespace App\Http\Controllers;

use App;
use Lang;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class PromoterController extends Controller 
{

    var $response = [];
    var $filter = [
        "searchText" => "",
        "itemPerPage" => 15,
        "orderby" => 'asc'
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
            $promoters = App\User::where('role' , 'promoter');   
            $this->response["totalItems"] = $promoters->count(); 
            $this->response['data'] = $promoters->latest()
                                                ->offset($this->filter["itemPerPage"] * ($page - 1))
                                                ->limit($this->filter["itemPerPage"])
                                                ->get();

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
            $user = App\User::where('role' , 'promoter');

            if(isset($filter['searchText'])){
                $user = $user->where(function($query) use($filter){
                                $query->where('first_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                        ->orWhere('last_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                        ->orWhere('mobile' , 'LIKE' , '%'.$filter['searchText'].'%')
                                        ->orWhere('email' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }    
            $this->response["totalItems"] = $user->count();
            $this->response['data'] = $user->orderBy('id' , $filter["orderby"] ? $filter["orderby"] : $this->filter["orderby"])
                                            ->offset($filter["itemPerPage"] ? $filter["itemPerPage"] * ($page - 1) : $this->filter["itemPerPage"] * ($page - 1))
                                            ->limit($filter["itemPerPage"] ? $filter["itemPerPage"] : $this->filter["itemPerPage"])
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
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'mobile'  => 'required|unique:users,mobile',
            'country_iso' => 'required|exists:countries,iso',
            'password' => 'required'
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
                $user = new App\User();
                $user->first_name = $request->input('first_name');
                $user->last_name = $request->input('last_name');
                $user->email = $request->input('email');
                $user->mobile = !Str::contains($request->input('mobile') , $request->input('dialCode')) && $request->input('dialCode') ? $request->input('dialCode'). $request->input('mobile') : $request->input('mobile') ;
                $user->password = Hash::make($request->input('password')); //str_random(8)
                $user->role = 'promoter';
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
            $user = App\User::find($id);
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
            'mobile' => 'required|unique:users,mobile,'.$id,
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
                $user = App\User::find($id);
                $user->first_name = $request->input('first_name');
                $user->last_name = $request->input('last_name');
                $user->email = $request->input('email');
                $user->mobile = !Str::contains($request->input('mobile') , $request->input('dialCode')) && $request->input('dialCode') ? $request->input('dialCode'). $request->input('mobile') : $request->input('mobile') ;
                $user->role = $request->has('role') ? $request->input('role') : 'promoter';
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
        try{
            $user = App\User::find($id);
            if(!isset($user)){
                $this->response["message"] = 'Promoter not found.!';
            }
            $user->update(['status' => false]);
            $this->response["message"] = 'Promoter deactivated successfull.!';
            $this->response["data"] = $user;
            return $this->successResponse($this->response);
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function Activated($id)
    {
        try{
            $user = App\User::find($id);
            if(!isset($user)){
                $this->response["message"] = 'Promoter User not found.!';
            }
            $user->update(['status' => true]);
            $this->response["message"] = 'Promoter User activated.!';
            $this->response["data"] = $user;
            return $this->successResponse($this->response);
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function change_password(Request $request , $id)
    {
        $validator = Validator::make($request->all() , [
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);

        try {
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = "Password update failed.!";
                return $this->errorResponse($this->response);
            } 
            $user = App\User::find($id);

            $user->password = Hash::make($request->password);
            $user->save();
            $this->response["message"] = "Password Update Successfully.!";
            $this->response['data'] = [];
            return $this->successResponse($this->response);

        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function info($id)
    {
        try{
            $user = App\User::find($id);
            $toArray = [
                "id" => $user->id,
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "fullname" => $user->fullname,
                "email" => $user->email,
                "mobile" => $user->mobile,
            ];
            $toArray['purchases'] = App\Purchase::where('user_id' , $id)->where('status' , true)->count();
            $toArray['purchase_amounts'] = App\Purchase::where('user_id' , $id)->where('status' , true)->sum('amount');

            $toArray['cards'] = App\ScratchCard::where('user_id' , $id)->where('status' , true)->count();

            $toArray['vouchers'] = App\Voucher::where('user_id' , $id)->where('status' , true)->count();
            $toArray['spinners'] = App\SpinWinner::where('user_id' , $id)->where('status' , true)->count();

            $toArray['user'] = Auth::user();

            $this->response["data"] = $toArray;          

            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }
}