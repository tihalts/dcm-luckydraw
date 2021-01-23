<?php 

namespace App\Http\Controllers;

use App;
use Lang;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\RewardPointVoucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class RewardPointController extends Controller 
{

    var $response = [];
    var $filter = [
        "searchText" => "",
        "itemPerPage" => 15,
        "orderby" => "desc",
        "active" => "all",
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
            $vouchers = App\Voucher::where('status' , true); 

            $this->response["totalItems"] = $vouchers->count();
            $this->response['data'] = $vouchers->with(['Customer' , 'Campaign' , 'Provider'])
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
            $vouchers = App\Voucher::where('status' , true);

            if(isset($filter['customer_id'])){
                $vouchers = $vouchers->where('customer_id' , $filter['customer_id']);
            } 
            
            if(isset($filter['promoter_id'])){
                $vouchers = $vouchers->where('user_id' , $filter['promoter_id']);
            } 
            
            if(isset($filter['searchText'])){
                $vouchers = $vouchers->where('code' , 'LIKE' , '%'.$filter['searchText'].'%')
                                       ->orWhereHas('customer' , function($query) use($filter){
                                            $query->where(function($query) use($filter){
                                                $query->where('first_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('last_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('mobile' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('cpr' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('email' , 'LIKE' , '%'.$filter['searchText'].'%');
                                            });
                                       })->orWhereHas('Campaign' , function($query) use($filter){
                                            $query->where(function($query) use($filter){
                                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
                                            });
                                       });
            }    


            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $vouchers = $vouchers->whereDate('created_at' , '>=' , $start_date)
                                                 ->whereDate('created_at' , '<=' , $end_date);
            }
            
            $this->response["totalItems"] = $vouchers->count();

            if(isset($filter['filter_by'])){
                $vouchers = $vouchers->orderBy($filter['filter_by'] , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"]);
            }else{
                $vouchers = $vouchers->orderBy('id' , 'desc');
            }

            $this->response['data'] = $vouchers->with(['Customer' , 'Campaign' , 'Provider'])
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
            'voucher_code' => 'required|unique:vouchers,code',
            'campaign_id' => 'required',
        ]);

       
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $campaign = App\Campaign::find($request->input('campaign_id'));
                $voucher = new App\Voucher();
                $voucher->code = $request->input('voucher_code');
                $voucher->voucher_amount = isset($campaign->data['amount']) ? $campaign->data['amount'] : 0;
                $voucher->campaign_id = $request->input('campaign_id');
                $voucher->customer_id =  $request->input('customer_id');
                $voucher->redeemed_at = Carbon::now();
                $voucher->user_id =  Auth::id();
                $voucher->status = true;
                $voucher->save();

                //event(new RewardPointVoucher($voucher));

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $this->limit($voucher->campaign_id , $voucher->customer_id);
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
            $voucher = App\Voucher::with(['voucher' , 'voucher.customer'])->find($id);
            $toArray = [
              'voucher_code' => $voucher->name ,
              'customer' => $voucher->voucher->customer ,
              'voucher' => $voucher->voucher ,
              'expires_at' => $voucher->expires_at ,
            ];
            $this->response["data"] = $toArray;
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function redeemed($voucher_id)
    {
        try{
            $voucher = App\Voucher::find($voucher_id);
            $voucher->update(['redeemed_at' => Carbon::now()]);
            $this->response["data"] = $voucher;
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function update(Request $request , $id)
    {      
        $validator = Validator::make($request->all(), [
            'voucher_code' => 'required|unique:vouchers,code,' .$id,
        ]);

       
        try {
            $this->response["title"] = Lang::get('');
        
            if ($validator->fails()) {
                $this->response["errors"] = $validator->errors();
                $this->response["old_data"] = $request->all();
                $this->response["message"] = Lang::get('');
                return $this->errorResponse($this->response);
            } else {
                $voucher = App\Voucher::find($id);
                if(isset($voucher)){
                    $voucher->update(['code' => $request->input('voucher_code')]);
                }
                

                $this->response["message"] = Lang::get('');
                $this->response["data"] = $this->limit($voucher->campaign_id , $voucher->customer_id);
                return $this->successResponse($this->response);
            }
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function limit($campaign_id , $customer_id)
    {
        $campaign = App\Campaign::find($campaign_id);
        
        $data = $campaign->data;
        $voucher_count = 0; 
        if(!isset($campaign)){
            return $voucher_count;
        }
        $remaining_customer_vouchers = $data['customer_limit'] - App\Voucher::where('campaign_id' , $campaign_id)->where('customer_id' ,$customer_id)->where('status' , true)->count();
        $remaining_today_vouchers = $data['day_limit'] - App\Voucher::whereDate('created_at' , Carbon::now())->where('status' , true)->count();
        $remaining_campaign_vouchers = $data['max_limit'] - App\Voucher::where('campaign_id' , $campaign_id)->where('status' , true)->count();    
       
        

        $start_at = $campaign->start_at ? $campaign->start_at : Carbon::now();
        $end_at = $campaign->end_at ? $campaign->end_at : Carbon::now();

        $purchase_amount = App\Purchase::whereBetween('created_at' ,[$start_at , $end_at])->where('customer_id' ,$customer_id)->where('status' , true)->sum('amount');
        $voucher_amount = App\Voucher::where('campaign_id' , $campaign_id)->where('customer_id' ,$customer_id)->where('status' , true)->sum('voucher_amount');

        if($remaining_customer_vouchers <= 0 && $data['customer_limit'] != 0){
            return $voucher_count;
        }

        if($remaining_today_vouchers <= 0 && $data['day_limit'] != 0){
            return $voucher_count;
        }

        if($remaining_campaign_vouchers <= 0 && $data['max_limit'] != 0){
            return $voucher_count;
        }
        
        $customer_remaining_campaign_amount = $purchase_amount - $voucher_amount;

        if($customer_remaining_campaign_amount < $data['amount']){
            return $voucher_count;
        }

        if($data['amount'] != 0){
            $voucher_count = floor($customer_remaining_campaign_amount / $data['amount']);
        } 
        
        if($data['customer_limit'] != 0){
            if($remaining_customer_vouchers < $voucher_count){
                $voucher_count = $remaining_customer_vouchers;
            }
        }
        
        if($data['day_limit'] != 0){
            if($remaining_today_vouchers < $voucher_count){
                $voucher_count = $remaining_today_vouchers;
            }
        }

        if($data['max_limit'] != 0){
            if($remaining_campaign_vouchers < $voucher_count){
                $voucher_count = $remaining_campaign_vouchers;
            }  
        }
        
        if($voucher_count < 0){
            $voucher_count = 0; 
        }

        return $voucher_count;
    }

    public function destroy($id)
    {
        try{
            $voucher = App\Voucher::find($id);
            if(!isset($voucher)){
                $this->response['message'] = "Voucher not found.!.";
            }
            $voucher->update(['status' => false]);

            $this->response["message"] = "Voucher remove successfully.!";
            $this->response["data"] = $voucher;
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }
}