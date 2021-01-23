<?php 

namespace App\Http\Controllers;

use App;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\CreatePurchase;
use App\Events\CustomerRegister;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class CustomerController extends Controller 
{

    var $response = [];
    var $filter = [
        "searchText" => "",
        "itemPerPage" => 15,
        "orderby" => "desc",
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
            $customers = App\Customer::where('status' , true);
            
            if(Auth::user()->role == 'promoter'){
                $customers = $customers->where('created_user_id' , Auth::id() );
            }
            $this->response["totalItems"] = $customers->count();
            $this->response['data'] = $customers->with('CreatedBy')->latest()
                                                ->offset($this->filter["itemPerPage"] * ($page - 1))
                                                ->limit($this->filter["itemPerPage"])
                                                ->get();
            
            $this->response["filter"] = $this->filter;
            $this->response["currentPage"] = $page;
            $this->response['user'] = Auth::user();
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
            $customers = App\Customer::where('status' , true);

            if(Auth::user()->role == 'promoter'){
                $customers = $customers->where('created_user_id' , Auth::id() );
            }

            if(isset($filter['searchText'])){
                $customers = $customers->where(function($query) use($filter){
                                $query->where('first_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                        ->orWhere('last_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                        ->orWhere('mobile' , 'LIKE' , '%'.$filter['searchText'].'%')
                                        ->orWhere('cpr' , 'LIKE' , '%'.$filter['searchText'].'%')
                                        ->orWhere('email' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }



            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $customers = $customers->whereDate('created_at' , '>=' , $start_date)
                                                 ->whereDate('created_at' , '<=' , $end_date);
            }
            
            $this->response["totalItems"] = $customers->count();

            if(isset($filter['filter_by'])){
                $customers = $customers->orderBy($filter['filter_by'] , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"]);
            }else{
                $customers = $customers->orderBy('id' , 'desc');
            }

            $this->response['data'] = $customers->with('CreatedBy')
                                            ->offset(isset($filter["itemPerPage"]) ? $filter["itemPerPage"] * ($page - 1) : $this->filter["itemPerPage"] * ($page - 1))
                                            ->limit(isset($filter["itemPerPage"]) ? $filter["itemPerPage"] : $this->filter["itemPerPage"])
                                            ->get();
            
            $this->response["filter"] = $filter;
            $this->response["currentPage"] = $page;
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
            'email' => 'required|email|max:255|unique:customers,email',
            'mobile'  => 'required|unique:customers,mobile',
            'cpr' => 'required|unique:customers,cpr',
            'country_iso' => 'required|exists:countries,iso'
            //'password' => 'required|min:6',
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
                $user = new App\Customer();
                $user->first_name = $request->input('first_name');
                $user->last_name = $request->input('last_name');
                $user->email = $request->input('email');
                $user->mobile = !Str::contains($request->input('mobile') , $request->input('dialCode')) && $request->input('dialCode') ? $request->input('dialCode'). $request->input('mobile') : $request->input('mobile') ;
                $user->cpr = $request->input('cpr');
                $user->country_iso = $request->input('country_iso');
                $user->nationality = $request->has('nationality.iso') ? $request->input('nationality.iso') : "";
                $user->phone_code = $country->phone_code;
                $user->password = Hash::make(str_random(8));
                $user->created_user_id = Auth::id();
                $user->save();

                event(new CustomerRegister($user));  
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
            $user = App\Customer::find($id);
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
            'email' => 'required|email|max:255|unique:customers,email,' .$id,
            'mobile' => 'required|unique:customers,mobile,'.$id,
            'cpr' => 'required|unique:customers,cpr,'.$id,
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
                $user = App\Customer::find($id);
                $user->first_name = $request->input('first_name');
                $user->last_name = $request->input('last_name');
                $user->email = $request->input('email');
                $user->mobile = !Str::contains($request->input('mobile') , $request->input('dialCode')) && $request->input('dialCode') ? $request->input('dialCode'). $request->input('mobile') : $request->input('mobile') ;
                $user->cpr = $request->input('cpr');
                $user->country_iso = $request->input('country_iso');
                $user->nationality = $request->has('nationality.iso') ? $request->input('nationality.iso') : "";
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

    public function purchase(Request $request)
    {
        $new_user = true;        
        if($request->has('cpr')){
            $user = App\Customer::where('cpr' , $request->input('cpr'))->first();
        }
        if($request->has('id') && !isset($user)){
            $user =  App\Customer::find($request->input('id'));
        }

        if(!isset($user)){            
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:customers,email',
                'mobile'  => 'required|unique:customers,mobile',
                'cpr' => 'required|unique:customers,cpr',
                'country_iso' => 'required|exists:countries,iso',
                'purchases' =>  'required|array',
            ]);
        } else{
            $new_user = false;
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:customers,email,' .$user->id,
                'mobile' => 'required|unique:customers,mobile,'.$user->id,
                'cpr' => 'required|unique:customers,cpr,'.$user->id,
                'country_iso' => 'required|exists:countries,iso',
                'purchases' =>  'required|array',
            ]);
        }

        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $country = App\Country::where('iso' , $request->input('country_iso'))->first();

                if(!isset($user)){     
                    $user = new App\Customer();               
                    $user->password = Hash::make(str_random(8));
                    $user->created_user_id = Auth::id();
                }
                
                $user->first_name = $request->input('first_name');
                $user->last_name = $request->input('last_name');
                $user->email = $request->input('email');
                $user->mobile = !Str::contains($request->input('mobile') , $request->input('dialCode')) && $request->input('dialCode') ? $request->input('dialCode'). $request->input('mobile') : $request->input('mobile') ;
                $user->cpr = $request->input('cpr');
                $user->country_iso = $request->input('country_iso'); 
                $user->phone_code = $country->phone_code;
                $user->nationality = $request->has('nationality.iso') ? $request->input('nationality.iso') : "";
                $user->save();

                $purchases = $request->input('purchases');

                foreach ($purchases as $key => $value) {
                    if(!isset($value['shop']['id']) && !isset($value['amount'])) continue;
                    if($value['amount'] <= 0) continue;
                    $purchase = new App\Purchase();
                    $purchase->purchase_no = Str::random(3).mt_rand(100000,999999);
                    $purchase->amount = $value['amount'];
                    $purchase->customer_id = $user->id;
                    $purchase->points = intval($value['amount']);
                    $purchase->user_id = Auth::id();
                    $purchase->shop_id = $value['shop']['id'];
                    $purchase->save();
                }

                $auth_user = Auth::user();
                $auth_user->updateCustomerId($user->id);
                event(new CreatePurchase($user , $auth_user));  
                
                if($new_user){
                   event(new CustomerRegister($user));  
                }

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $user;
                return $this->successResponse($this->response);
            }
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }    

    public function ScratchCardCount($customer_id)
    {
        $customer = App\Customer::find($customer_id);
        $auth_user = Auth::user();
        $auth_user->updateCustomerId($customer->id);
        event(new CreatePurchase($customer , $auth_user));
        return App\ScratchCardRequest::where('customer_id' , $customer_id)
                                       ->where('status', true)
                                       //->whereNull('scratched_at')
                                       ->count();
    }

    public function destroy($id)
    {
        try{
            $customer = App\Customer::find($id);
            if(!isset($customer)){
                $this->response['message'] = "Customer not found.!.";
            }
            $customer->update(['status' => false]);

            $this->response["message"] = "Customer remove successfully.!";
            $this->response["data"] = $customer;
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function getPurchaseNumber()
    {
        $last = App\Purchase::orderBy('created_at', 'desc')->first();        
        $number = empty($last) ? 0 : substr($last->purchase_no, 3);
        return 'DMO' . sprintf('%06d', intval($number) + 1);
    }

    public function info($id)
    {
        try{
            $user = App\Customer::find($id);
            $toArray = [
                "id" => $user->id,
                "first_name" => $user->first_name,
                "last_name" => $user->last_name,
                "fullname" => $user->fullname,
                "email" => $user->email,
                "mobile" => $user->mobile,
                "cpr" => $user->cpr,
                "country" => $user->getNationalityAttribute($user->country_iso)
            ];
            $toArray['purchases'] = App\Purchase::where('customer_id' , $id)->where('status' , true)->count();
            $toArray['purchase_amounts'] = App\Purchase::where('customer_id' , $id)->where('status' , true)->sum('amount');

            $toArray['cards'] = App\ScratchCard::where('customer_id' , $id)->where('status' , true)->count();
            $toArray['card_wins'] = App\ScratchCard::where('customer_id' , $id)->where('is_winner' , true)->where('status' , true)->count();

            $toArray['vouchers'] = App\Voucher::where('customer_id' , $id)->where('status' , true)->count();

            $toArray['raffle_draws'] = App\Winner::where('customer_id' , $id)->where('status' , true)->count();
            $toArray['spinners'] = App\SpinWinner::where('customer_id' , $id)->where('status' , true)->count();
            $toArray['free_gifts'] = App\GiftVoucher::where('customer_id' , $id)->where('status' , true)->count();

            $toArray['user'] = Auth::user();

            $this->response["data"] = $toArray;          

            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function raffledraw(Request $request , $id  , $page = 1)
    {     
        try {   

            $filter = $request->all();         
            $winners = App\Winner::where('customer_id' , $id);

            if(isset($filter['searchText'])){
                $winners = $winners->whereHas('LuckyDraw', function($query) use($filter){
                                            $query->where('name' ,  'LIKE' , '%'.$filter['searchText'].'%');
                                        });
            }   

            $this->response["totalItems"] = $winners->count();
            $this->response["currentPage"] = $page;

            $this->response['data'] = $winners->with(['LuckyDraw'])
                                                ->orderBy('id' , $filter["orderby"] ? $filter["orderby"] : $this->filter["orderby"])
                                                ->offset($filter["itemPerPage"] ? $filter["itemPerPage"] * ($page - 1) : $this->filter["itemPerPage"] * ($page - 1))
                                                ->limit($filter["itemPerPage"] ? $filter["itemPerPage"] : $this->filter["itemPerPage"])
                                                ->get(); 

            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }
}