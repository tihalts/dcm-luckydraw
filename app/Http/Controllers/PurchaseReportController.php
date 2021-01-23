<?php 

namespace App\Http\Controllers;

use App;
use Lang;
use App\Purchase;
use App\Campaign;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class PurchaseReportController extends Controller 
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

    public function customerByCountry()
    {
        try {  
            $toArray = []; 
            $countries = DB::table('countries')
                            ->select([
                                'countries.*',
                                DB::raw("count(customers.id) as total_users")
                            ])
                            ->leftJoin('customers','users.country_iso','=','customers.iso')
                            ->groupBy('customers.country_iso')
                            ->orderBy('total_users' , 'desc')
                            ->take(10)
                            ->get();     
                         
            $other = DB::table('customers')
                        ->leftJoin('countries','customers.country_iso','=','countries.iso')
                        ->count();

            $user_count = 0;

            foreach ($countries as $key => $country) {
                $toArray[] = [ $country->name , $country->total_users ];
                $user_count += $country->total_users;
            }

            $toArray[] = [ 'others' , $other - $user_count ];


            $this->response['data'] = $toArray;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }    

    public function vouchers(Request $request)
    {
        try {  
            $toArray = []; 
            $vouchers = DB::table('users')
                            ->select([
                                'users.id',
                                DB::raw("CONCAT(`first_name`,' ', `last_name`) AS fullname"),
                                DB::raw("count(vouchers.id) as total_vouchers")
                            ])
                            ->leftJoin('vouchers','users.id','=','vouchers.customer_id')
                            ->groupBy('users.id')
                            ->orderBy('total_vouchers' , 'desc')
                            ->take(10)
                            ->get();         

            $other = DB::table('vouchers')
                            ->leftJoin('users','users.id','=','vouchers.customer_id')
                            ->count();   
                            
            $top_vouchers = 0;

            foreach ($vouchers as $key => $voucher) {
                $toArray[] = [ $voucher->fullname ,  (int) $voucher->total_vouchers ];
                $top_vouchers += $voucher->total_vouchers;
            }

            $toArray[] = [ 'others' ,  $other - $top_vouchers ];


            $this->response['data'] = $toArray;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function scratchcardByCampaign(Request $request)
    {
        try {  
            $toArray = []; 
            $scratchcards = DB::table('campaigns')
                            ->select([
                                'campaigns.*',
                                DB::raw("count(scratch_cards.id) as total_scratch_cards")
                            ])
                            ->leftJoin('scratch_cards','campaigns.id','=','scratch_cards.campaign_id')
                            ->groupBy('scratch_cards.campaign_id')
                            ->orderBy('total_scratch_cards' , 'desc')
                            ->take(10)
                            ->get();         

            $other = DB::table('scratch_cards')
                            ->leftJoin('campaigns','campaigns.id','=','scratch_cards.campaign_id')
                            ->count();   
                            
            $total_scratch_cards = 0;

            foreach ($scratchcards as $key => $scratch_card) {
                $toArray[] = [ $scratch_card->name ,  (int) $scratch_card->total_scratch_cards ];
                $total_scratch_cards += $scratch_card->total_scratch_cards;
            }

            $toArray[] = [ 'others' ,  $other - $total_scratch_cards ];


            $this->response['data'] = $toArray;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function voucherByCampaign(Request $request)
    {
        try {  
            $toArray = []; 
            $vouchers = DB::table('campaigns')
                            ->select([
                                'campaigns.*',
                                DB::raw("count(vouchers.id) as total_vouchers")
                            ])
                            ->leftJoin('vouchers','campaigns.id','=','vouchers.campaign_id')
                            ->groupBy('vouchers.campaign_id')
                            ->orderBy('total_vouchers' , 'desc')
                            ->take(10)
                            ->get();         

            $other = DB::table('vouchers')
                            ->leftJoin('campaigns','campaigns.id','=','vouchers.campaign_id')
                            ->count();   
                            
            $total_vouchers = 0;

            foreach ($vouchers as $key => $voucher) {
                $toArray[] = [ $voucher->name ,  (int) $voucher->total_vouchers ];
                $total_vouchers += $voucher->total_vouchers;
            }

            $toArray[] = [ 'others' ,  $other - $total_vouchers ];


            $this->response['data'] = $toArray;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function campaigns(Request $request)
    {
        try {      
            $filter = $request->all(); 
            $purchases = App\Purchase::where('status' , true); 

            if(isset($filter['start_date']) && isset($filter['end_date']) && !isset($filter['campaign_id'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $purchases = $purchases->whereBetween('created_at' ,[$start_date , $end_date]);
            }

            $this->response['data'] = $purchases->with(['customer' , 'user' , 'shop'])
                                                ->orderBy('id' , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"])
                                                ->offset(isset($filter["itemPerPage"]) ? $filter["itemPerPage"] * ($page - 1) : $this->filter["itemPerPage"] * ($page - 1))
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

    public function campaign(Request $request)
    {
        try {      
            $filter = $request->all(); 
            $purchases = App\Purchase::where('status' , true);  
            $page = isset($filter['page']) ? $filter['page'] : 1;
            
            if(isset($filter['user_id'])){
                $purchases = $purchases->where('user_id' , $filter['user_id']);
            }

            if(isset($filter['customer_id'])){
                $purchases = $purchases->where('customer_id' , $filter['customer_id']);
            }

            if(isset($filter['campaign_id'])){
                $campaign = App\Campaign::find($filter['campaign_id']);
                if(isset($campaign)){
                    $campaign_start_at = Carbon::parse($campaign->start_at);
                    $campaign_end_at = Carbon::parse($campaign->end_at);
                    if(isset($filter['start_date']) && isset($filter['end_date'])){
                        $start_date = Carbon::parse($filter['start_date']);
                        $end_date = Carbon::parse($filter['end_date']);
                        $campaign_start_at = $campaign_start_at > $start_date ? $campaign_start_at : $start_date;
                        $campaign_end_at = $campaign_end_at > $end_date ? $campaign_end_at : $end_date;
                    }
                    $purchases = $purchases->whereBetween('created_at' ,[$campaign_start_at , $campaign_end_at]);
                }
            }

            if(isset($filter['shop_id'])){
                $purchases = $purchases->where('shop_id' , $filter['shop_id']);
            }else if(isset($filter['category_id'])){
                $shop_ids = App\Shop::where('business_type_id' , $filter['category_id'])->get()->pluck('id');
                $purchases = $purchases->whereIn('shop_id' , $shop_ids);
            }

            if(isset($filter['start_date']) && isset($filter['end_date']) && !isset($filter['campaign_id'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $purchases = $purchases->whereBetween('created_at' ,[$start_date , $end_date]);
            }

            $this->response["totalItems"] = $purchases->count();
            $this->response['data'] = $purchases->with(['customer' , 'user' , 'shop'])
                                                ->orderBy('id' , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"])
                                                ->offset(isset($filter["itemPerPage"]) ? $filter["itemPerPage"] * ($page - 1) : $this->filter["itemPerPage"] * ($page - 1))
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

    public function saleByDate(Request $request)
    {
        try {      
            $filter = $request->all(); 
            
            $purchases = App\Purchase::where('status' , true);  

            $start_date = Carbon::now()->subDays(7)->startOfDay();
            $end_date = Carbon::now();

            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date']);
                $end_date = Carbon::parse($filter['end_date']);
            }

            $purchases = $purchases->whereBetween('created_at' ,[$start_date , $end_date]);

            $datas = $purchases->get(); 

            $this->response['data'] = $datas; 

            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }   

    public function purchaseByCustomer(Request $request , $page = 1)
    {
        try {      
            $filter = $request->all(); 
            $purchases = App\Purchase::where('status' , true); 

            if(isset($filter['start_date']) && isset($filter['end_date']) && !isset($filter['campaign_id'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $purchases = $purchases->whereBetween('created_at' ,[$start_date , $end_date]);
            }

            $this->response['data'] = $purchases->select(DB::raw('count(DISTINCT customer_id) as `customers`'), DB::raw("DATE_FORMAT(created_at, '%m-%Y') date"),  DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
                                                ->groupby('year','month')
                                                //->orderBy('date' , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"])
                                                ->offset(isset($filter["itemPerPage"]) ? $filter["itemPerPage"] * ($page - 1) : $this->filter["itemPerPage"] * ($page - 1))
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

    public function purchaseAmountCustomer(Request $request , $page = 1)
    {
        try {      
            $filter = $request->all(); 
            $purchases = App\Purchase::where('status' , true); 

            if(isset($filter['start_date']) && isset($filter['end_date']) && !isset($filter['campaign_id'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $purchases = $purchases->whereBetween('created_at' ,[$start_date , $end_date]);
            }
            //SELECT YEAR(created_at), MONTH(created_at), COUNT(DISTINCT customer_id) AS 'Count' FROM purchases GROUP BY YEAR(created_at), MONTH(created_at);
            //SELECT YEAR(created_at) as year, MONTH(created_at) as month, COUNT(*) as total, sum(amount) as amount FROM purchases GROUP BY YEAR(created_at), MONTH(created_at);
            $this->response['data'] = $purchases->select(DB::raw('sum(amount) as `amount`'), DB::Raw('COUNT(*) as `total`'), DB::raw("DATE_FORMAT(created_at, '%m-%Y') date"),  DB::raw('YEAR(created_at) year, MONTH(created_at) month'))
                                                ->groupby('year','month')
                                                //->orderBy('date' , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"])
                                                ->offset(isset($filter["itemPerPage"]) ? $filter["itemPerPage"] * ($page - 1) : $this->filter["itemPerPage"] * ($page - 1))
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
}