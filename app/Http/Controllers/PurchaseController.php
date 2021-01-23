<?php 

namespace App\Http\Controllers;

use App;
use Lang;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\CreatePurchase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class PurchaseController extends Controller 
{

    var $response = [];
    var $filter = [
        "searchText" => "",
        "orderby" => "desc",
        "itemPerPage" => 10
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
            $purchases = App\Purchase::where('status' , true);  
            
            if(Auth::user()->role == 'promoter'){
                $purchases = $purchases->where('user_id' , Auth::id() );
            }
            $this->response["totalItems"] = $purchases->count();
            $this->response['data'] = $purchases->with(['customer' , 'user' , 'shop'])
                                                ->latest()
                                                ->offset($this->filter["itemPerPage"] * ($page - 1))
                                                ->limit($this->filter["itemPerPage"])
                                                ->get();
            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
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
            $purchases = App\Purchase::where('status' , true);  
            
            if(Auth::user()->role == 'promoter'){
                $purchases = $purchases->where('user_id' , Auth::id() );
            }

            if(isset($filter['customer_id'])){
                $purchases = $purchases->where('customer_id' , $filter['customer_id']);
            }  

            if(isset($filter['promoter_id'])){
                $vouchers = $purchases->where('user_id' , $filter['promoter_id']);
            } 

            if(isset($filter['searchText'])){

                $purchases = $purchases->where('purchase_no' , 'LIKE' , '%'.$filter['searchText'].'%')
                                       ->orWhereHas('customer' , function($query) use($filter){
                                            $query->where(function($query) use($filter){
                                                $query->where('first_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('last_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('mobile' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('cpr' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('email' , 'LIKE' , '%'.$filter['searchText'].'%');
                                            });
                                       })->orWhereHas('user' , function($query) use($filter){
                                            $query->where(function($query) use($filter){
                                                $query->where('first_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('last_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('mobile' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('email' , 'LIKE' , '%'.$filter['searchText'].'%');
                                            });
                                       });
            }    

            $this->response["totalItems"] = $purchases->count();
            $this->response['data'] = $purchases->with(['customer' , 'user' , 'shop'])
                                                ->orderBy('id' , $filter["orderby"] ? $filter["orderby"] : $this->filter["orderby"])
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
            'amount' => 'required|min:0|numeric',
            'customer_id' => 'required|exists:customers,id',
            'shop_id' => 'required|exists:shops,id',
        ]);

       
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $purchase = new App\Purchase();
                $purchase->purchase_no = Str::random(3).mt_rand(100000,999999);
                $purchase->amount = $request->input('amount');
                $purchase->customer_id = $request->input('customer_id');
                $purchase->points = intval($request->input('amount'));
                $purchase->user_id = Auth::id();
                $purchase->shop_id = $request->input('shop_id');
                $purchase->save();

                $customer = App\Customer::find($purchase->customer_id);

                event(new CreatePurchase($customer, Auth::user()));

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $purchase;
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
            $purchase = App\Purchase::with(['ScratchCards' , 'Vouchers'])->find($id);
            $this->response["data"] = $purchase;
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function update(Request $request , $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric',
            'customer_id' => 'required|exists:customers,id',
        ]);
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $purchase = App\Purchase::find($id);
                $purchase->amount = $request->input('amount');
                $purchase->customer_id = $request->input('customer_id');
                $purchase->points = 10;
                $purchase->save();

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $purchase;
                return $this->successResponse($this->response);
            }
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function searchCustomer(Request $request)
    {

        try {      
            $filter = $request->all(); 
            $customers = App\Customer::where('status' , true);

            if(isset($filter['searchText'])){

                $customers = $customers->where(function($query) use($filter) {
                                            $query->where('first_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                    ->orWhere('last_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                    ->orWhere('cpr' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                    ->orWhere('email' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                    ->orWhere('mobile' , 'LIKE' , '%'.$filter['searchText'].'%');
                                        });
            }    

            $this->response['data'] = $customers->limit(15)->get();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function destroy($id)
    {
        try {    
            $purchase = App\Purchase::where('id', $id)->update(['status' => false]);
            $lucky_draw_points = App\LuckyDrawPoint::where('purchase_id' , $id)->update(['status' => false]);
            $this->response["message"] = "Delete user successfull.!";
            $this->response['data'] = [];
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function getPoints($amount = 0) : int
    {
        if($amount == 0) return 0;
        $settings = App\SiteSetting::where('status' , true)->get()->pluck('value' , 'key');
        $settings['points'] = isset($settings['points']) ? json_decode($settings['points'] , true) : [];
        $settings['otherwise'] = isset($settings['otherwise']) ? json_decode($settings['otherwise'] , true) : [];   
        try{
            foreach ($settings['points'] as $key => $value) {
                if( $value['amount_from'] <= $amount && $value['amount_from'] >= $amount){
                    return $value['points'];
                    break;
                }
            }  
            if(isset($settings['otherwise']['amount']))   {
                return $settings['otherwise']['amount'] <= $amount ? $settings['otherwise']['points'] : 0;
            }
            return 0;         
        } catch (\Exception $e){
            return 0;
        }
    }

    public function getPointSettings()
    {     
        try {            
            $settings = App\SiteSetting::where('status' , true)->get()->pluck('value' , 'key');
            $site_settings = [];
            $site_settings['points'] = isset($settings['points']) ? json_decode($settings['points'] , true) : [];
            $site_settings['otherwise'] = isset($settings['otherwise']) ? json_decode($settings['otherwise'] , true) : [];
            $this->response['data'] = $site_settings;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
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

    public function ScratchCard($purchase_id = 98){
        $purchase = App\Purchase::find($purchase_id);
        return $purchase->createScratchCards(1);

    }

    public function fetch_action()
    {
        try {            
            $user = Auth::user();
            $this->response['data'] = $user->getPurchaseAction();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function action(Request $request)
    {
        try {            
            $user = Auth::user();
            $data = $request->all();
            $this->response['data'] = $user->createPurchaseAction($data);
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function customer_action(Request $request , $customer_id)
    {
        try {            
            $user = Auth::user();
            $data = $request->all();
            $user->updateAction($customer_id, $data);
            $this->response['data'] = $user->getPurchaseAction();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }


    public function destory_action()
    {
        try {            
            $user = Auth::user();
            $this->response['data'] = $user->resetPurchaseAction();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function fetch_spinner_action()
    {
        try {            
            $user = Auth::user();
            $this->response['data'] = $user->getSpinnerAction();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function spinner_action(Request $request)
    {
        try {            
            $user = Auth::user();
            $data = $request->all();
            $this->response['data'] = $user->createSpinnerAction($data);
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function customer_spinner_action(Request $request , $customer_id)
    {
        try {            
            $user = Auth::user();
            $data = $request->all();
            $user->updateAction($customer_id, $data);
            $this->response['data'] = $user->getSpinnerAction();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }


    public function destory_spinner_action()
    {
        try {            
            $user = Auth::user();
            $this->response['data'] = $user->resetSpinnerAction();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }
}