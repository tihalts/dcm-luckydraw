<?php 

namespace App\Http\Controllers\Reports;

use App;
use Pdf;
use Lang;
use Excel;
use App\Purchase;
use App\Campaign;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use App\Exports\ReportExport;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class CustomerController extends Controller 
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

    public function Countries(Request $request)
    {
        try { 

            $filter = $request->all(); 
            $page = isset($filter['page']) ? $filter['page'] : 1;

            $countries = DB::table('customers')
                            ->select([
                                'countries.*',
                                DB::raw("count(customers.id) as total_users")
                            ])
                            ->leftJoin('countries','customers.country_iso','=','countries.iso')
                            ->groupBy('customers.country_iso')
                            ->orderBy('total_users' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                            ->offset(15 * ($page - 1))
                            ->limit(15)
                            ->get(); 


            $this->response['data'] = $countries;
            return $this->successResponse($this->response);

        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function TopCountries(Request $request)
    {
        try {  
            $toArray = []; 

            $filter = $request->all();
            $start_date = null;
            $end_date = null;

            $day_diff = 0;
            
            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $day_diff = $end_date->diffInDays($start_date);
            } 

            $countries = DB::table('customers')
                            ->select([
                                'countries.*',
                                DB::raw("count(customers.id) as total_users")
                            ])
                            ->leftJoin('countries','customers.country_iso','=','countries.iso')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->groupBy('customers.country_iso')
                            ->orderBy('total_users' , 'desc')
                            ->take(10)
                            ->get();     
                         
            $other = DB::table('customers')
                        ->leftJoin('countries','customers.country_iso','=','countries.iso')
                        ->where(function($query) use($start_date , $end_date) {
                            if(isset($start_date) && isset($end_date)){
                                $query->whereBetween("created_at" ,  [ $start_date , $end_date ]);
                            }                                                
                        })
                        ->count();

            $user_count = 0;
            $labels = [];
            foreach ($countries as $key => $country) {
                $labels[] = $country->name;
                $toArray[] = [ "x" => $country->name , "y" => $country->total_users ];
                $user_count += $country->total_users;
            }
            $labels[] = 'others';
            $toArray[] = [ "x" => 'others' , "y" => $other - $user_count ];


            $this->response['data'] = $toArray;
            $this->response['labels'] = $labels;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }    

    public function CountrySaleList(Request $request)
    {     
        try { 
            
            $filter = $request->all(); 
            $page = isset($filter['page']) ? $filter['page'] : 1;

            $countries = DB::table('countries')
                            ->select([
                                'countries.*',
                                DB::raw("IFNULL(sum(purchases.amount),0) as total")
                            ])
                            ->leftJoin('customers', 'customers.country_iso', '=' , 'countries.iso')
                            ->leftJoin('purchases','customers.id','=','purchases.customer_id')
                            ->groupBy('customers.country_iso')
                            ->where('purchases.status' , 1)
                            ->orderBy('total' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                            ->offset(15 * ($page - 1))
                            ->limit(15)
                            ->get(); 


            $this->response['data'] = $countries;
            return $this->successResponse($this->response);

        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }

    }

    public function SaleByCountries(Request $request)
    {     
        try {  
            
            $toArray = []; 

            $filter = $request->all();
            $start_date = null;
            $end_date = null;

            $day_diff = 0;
            
            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $day_diff = $end_date->diffInDays($start_date);
            } 

            $purchases = DB::table('countries')
                            ->select([
                                'countries.*',
                                DB::raw("IFNULL(sum(purchases.amount),0) as total")
                            ])
                            ->leftJoin('customers', 'customers.country_iso', '=' , 'countries.iso')
                            ->leftJoin('purchases','customers.id','=','purchases.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->where('purchases.status' , 1)
                            ->groupBy('countries.iso')
                            ->orderBy('total' , 'desc')
                            ->take(10)
                            ->get();         

            $other = DB::table('countries')
                            ->leftJoin('customers', 'customers.country_iso', '=' , 'countries.iso')
                            ->leftJoin('purchases','customers.id','=','purchases.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->where('purchases.status' , 1)
                            ->sum('purchases.amount');   
                            
            $top_amount = 0;

            $labels = [];
            foreach ($purchases as $key => $purchase) {
                $labels[] = $purchase->name;
                $toArray[] = [ "x" => $purchase->name , "y" => $purchase->total ];
                $top_amount += $purchase->total;
            }
            $labels[] = 'others';
            $toArray[] = [ "x" => 'others' , "y" => $other - $top_amount ];


            $this->response['data'] = $toArray;
            $this->response['labels'] = $labels;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function CustomerWiseVouchers(Request $request)
    {
        try {  
            $toArray = []; 

            $filter = $request->all();
            $start_date = null;
            $end_date = null;

            $day_diff = 0;
            
            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $day_diff = $end_date->diffInDays($start_date);
            } 

            $vouchers = DB::table('vouchers')
                            ->select([
                                'customers.id',
                                DB::raw("CONCAT(`first_name`,' ', `last_name`) AS fullname"),
                                DB::raw("count(vouchers.id) as total_vouchers")
                            ])
                            ->leftJoin('customers','customers.id','=','vouchers.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("vouchers.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->groupBy('customers.id')
                            ->orderBy('total_vouchers' , 'desc')
                            ->take(10)
                            ->get();         

            $other = DB::table('vouchers')
                            ->leftJoin('customers','customers.id','=','vouchers.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("vouchers.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->count();   
                            
            $top_vouchers = 0;
            $labels = [];
            foreach ($vouchers as $key => $voucher) {
                $labels[] = $voucher->fullname;
                $toArray[] = [ 'x' => $voucher->fullname ,  'y' => (int) $voucher->total_vouchers ];
                $top_vouchers += $voucher->total_vouchers;
            }

            $labels[] = 'others';

            $toArray[] = [ 'x' => 'others' ,  "y" =>$other - $top_vouchers ];

            $this->response['day_diff'] = $day_diff;
            $this->response['labels'] = $labels;
            $this->response['data'] = $toArray;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function DateWiseVouchers(Request $request)
    {
        try {  
            $toArray = []; 

            $start_date = null;
            $end_date = null;

            $day_diff = 0;
            $filter = $request->all();
            
            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $day_diff = $end_date->diffInDays($start_date);
            } 

            $vouchers = DB::table('vouchers')
                            ->select([
                                DB::raw('DATE(created_at) as date'),
                                DB::raw("count(vouchers.id) as total_vouchers")
                            ])
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("vouchers.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->groupBy(DB::raw('Date(created_at)'))
                            ->orderBy('created_at' , 'desc')
                            ->take(10)
                            ->get(); 
                            
            $labels = [];
            foreach ($vouchers as $key => $voucher) {
                $labels[] = $voucher->date;
                $toArray[] = [ 'x' => $voucher->date ,  'y' => (int) $voucher->total_vouchers ];
            }

            $this->response['day_diff'] = $day_diff;
            $this->response['labels'] = $labels;
            $this->response['data'] = $toArray;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function CampaignWiseVouchers(Request $request)
    {
        try {  
            $toArray = []; 

            $filter = $request->all();
            $start_date = null;
            $end_date = null;

            $day_diff = 0;
            
            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $day_diff = $end_date->diffInDays($start_date);
            } 

            $vouchers = DB::table('campaigns')
                            ->select([
                                'campaigns.id',
                                'campaigns.name',
                                DB::raw("count(vouchers.id) as total_vouchers")
                            ])
                            ->leftJoin('vouchers','campaigns.id','=','vouchers.campaign_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("vouchers.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->groupBy('campaigns.id')
                            ->orderBy('campaigns.created_at' , 'desc')
                            ->take(10)
                            ->get(); 

            $labels = [];
            foreach ($vouchers as $key => $voucher) {
                $labels[] = $voucher->name;
                $toArray[] = [ 'x' => $voucher->name ,  'y' => (int) $voucher->total_vouchers ];
            }


            $this->response['day_diff'] = $day_diff;
            $this->response['labels'] = $labels;
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

            $filter = $request->all();
            $start_date = null;
            $end_date = null;

            $day_diff = 0;
            
            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $day_diff = $end_date->diffInDays($start_date);
            } 

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

            $filter = $request->all();
            $start_date = null;
            $end_date = null;

            $day_diff = 0;
            
            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $day_diff = $end_date->diffInDays($start_date);
            } 
            
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

    public function campaigns(Request $request , $page = 1)
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
}