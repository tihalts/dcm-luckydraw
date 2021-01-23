<?php 

namespace App\Http\Controllers\Reports;

use App;
use PDF;
use Lang;
use Excel;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\ReportExport;
use App\Exports\PurchasesExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
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

    public function list(Request $request)
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

    public function export(Request $request)
    {
        try {      
            $filter = $request->all(); 
            $purchases = App\Purchase::where('status' , true); 
            
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

            $purchases = $purchases->with(['customer' , 'user' , 'shop' , 'shop.category'])
                                                ->get()
                                                ->toArray(); 

            foreach($purchases as $key => $purchase){
                $export[$key]['#'] = $purchase['purchase_no'];
                $export[$key]['Shop Name'] = isset($purchase['shop']) ? $purchase['shop']['name'] : null;
                $export[$key]['Category Name'] = isset($purchase['shop']['category']) ? $purchase['shop']['category']['name'] : null;
                $export[$key]['Purchase Amount'] = $purchase['amount'];
                $export[$key]['Customer Name'] = $purchase['customer']['fullname'];
                $export[$key]['Customer CPR'] = $purchase['customer']['cpr'];
                $export[$key]['Customer Email'] = $purchase['customer']['email'];
                $export[$key]['Customer mobile'] = $purchase['customer']['mobile'];
                $export[$key]['Provider By'] = isset($purchase['user']['fullname']) ? $purchase['user']['fullname'] : null;
                $export[$key]['Provider Role'] = isset($purchase['user']['role']) ? $purchase['user']['role'] : null;
                $export[$key]['Created At'] = $purchase['created_at'];

            }

            return Excel::download(new PurchasesExport($export), 'purchases.xlsx');
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function line_chart(Request $request)
    {
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
        }else{
            $purchases = $purchases->whereBetween('created_at' ,[ now()->subDays(30) , now() ]);
        }

        $purchases = $purchases->select(array(DB::Raw('sum(amount) as amount'),DB::Raw('DATE(created_at) day')))
                                ->groupBy('day')
                                ->orderBy('day')
                                ->get()
                                ->pluck('amount' , 'day'); 


        $this->response['data'] = $purchases;
        return $this->successResponse($this->response);
    }

    public function searchUsers(Request $request)
    {

        try {      
            $filter = $request->all(); 
            $users = App\User::where('status' , true);
            $page = isset($filter['page']) ? $filter['page'] : 1;
            if(isset($filter['searchText'])){

                $users = $users->where(function($query) use($filter) {
                                    $query->where('first_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                            ->orWhere('last_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                            ->orWhere('role' , 'LIKE' , '%'.$filter['searchText'].'%')
                                            ->orWhere('email' , 'LIKE' , '%'.$filter['searchText'].'%')
                                            ->orWhere('mobile' , 'LIKE' , '%'.$filter['searchText'].'%');
                                });
            }    

            $this->response['data'] = $users->limit(15)->get();
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function searchCampaigns(Request $request)
    {
        try {      
            $filter = $request->all(); 
            $campaigns = App\Campaign::where('status' , true);
            $page = isset($filter['page']) ? $filter['page'] : 1;

            if(isset($filter['campaign_type'])){
                $campaigns = $campaigns->where('campaign_type' , $filter['campaign_type']);
            }

            if(isset($filter['group_id'])){
                $campaigns = $campaigns->where('group_id' , $filter['group_id']);
            }

            if(isset($filter['searchText'])){
                $campaigns = $campaigns->where(function($query) use($filter){
                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                      ->orWhere('campaign_type' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }    

            $this->response["totalItems"] = $campaigns->count();
            $this->response['data'] = $campaigns->orderBy('id' , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"])
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

    public function export_pdf(Request $request , $type)
    {
        try {      
            $filter = $request->all(); 
            $purchases = App\Purchase::where('status' , true); 
            
            if(isset($filter['user_id'])){
                $purchases = $purchases->where('user_id' , $filter['user_id']);
            }

            if(isset($filter['customer_id'])){
                $purchases = $purchases->where('customer_id' , $filter['customer_id']);
            }
            $start_date = isset($filter['start_date']) ? Carbon::parse($filter['start_date']) : null;
            $end_date = isset($filter['end_date']) ?  Carbon::parse($filter['end_date']) : Carbon::now();

            if(!isset($start_date)){
                $start_date = Carbon::parse($end_date)->subDays(30);
            }

            if(isset($filter['campaign_id'])){
                $campaign = App\Campaign::find($filter['campaign_id']);
                if(isset($campaign)){
                    $campaign_start_at = Carbon::parse($campaign->start_at);
                    $campaign_end_at = Carbon::parse($campaign->end_at);
                    if(isset($start_date) && isset($start_date)){                        
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

            $purchases = $purchases->with(['customer' , 'user' , 'shop' , 'shop.category'])->limit(10)         
                                    ->get()
                                    ->toArray(); 

            foreach($purchases as $key => $purchase){
                $export[$key]['#'] = $purchase['purchase_no'];
                $export[$key]['Shop Name'] = isset($purchase['shop']) ? $purchase['shop']['name'] : null;
                $export[$key]['Category Name'] = isset($purchase['shop']['category']) ? $purchase['shop']['category']['name'] : null;
                $export[$key]['Purchase Amount'] = $purchase['amount'];
                $export[$key]['Customer Name'] = $purchase['customer']['fullname'];
                $export[$key]['Customer CPR'] = $purchase['customer']['cpr'];
                $export[$key]['Customer Email'] = $purchase['customer']['email'];
                $export[$key]['Customer mobile'] = $purchase['customer']['mobile'];
                $export[$key]['Provider By'] = isset($purchase['user']['fullname']) ? $purchase['user']['fullname'] : null;
                $export[$key]['Provider Role'] = isset($purchase['user']['role']) ? $purchase['user']['role'] : null;
                $export[$key]['Created At'] = $purchase['created_at'];

            }
            $headers = isset($export[0]) ? array_keys($export[0]) : [];
            // $pdf = PDF::loadView('exports.purchase' , [ 'headers' => $headers , 'purchases' => $export ]);
            // return $pdf->download('purchases.pdf');
            //return Excel::download(new App\Exports\PurchasesPDFExport($export)  , 'purchases.pdf', \Maatwebsite\Excel\Excel::DOMPDF);
            if($type == 'pdf'){
                $pdf = PDF::loadView('exports.purchase', ['headers' => $headers , 'purchases' => $export]);
                return $pdf->download('country_customers.pdf');
            }else {
                return Excel::download(new ReportExport($export), Uuid::uuid1(). '.xlsx');
            }
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function saleReports(Request $request)
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

            $purchases = DB::table('purchases')
                            ->select([
                                DB::raw('DATE(created_at) as date'),
                                DB::raw("count(purchases.amount) as total_amount")
                            ])
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->where('purchases.status' , 1)
                            ->groupBy(DB::raw('Date(created_at)'))
                            ->orderBy('created_at' , 'desc')
                            ->take(10)
                            ->get();    
                            
            $labels = [];
            foreach ($purchases as $key => $purchase) {
                $labels[] = $purchase->date;
                $toArray[] = [  'x' => $purchase->date , 'y' => (int) $purchase->total_amount ];
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

    public function saleByShop(Request $request)
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

            $shops = DB::table('shops')
                            ->select([
                                'shops.*',
                                DB::raw("count(purchases.id) as total_amount")
                            ])
                            ->leftJoin('purchases','shops.id','=','purchases.shop_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->where('purchases.status' , 1)
                            ->groupBy('purchases.shop_id')
                            ->orderBy('total_amount' , 'desc')
                            ->take(10)
                            ->get();         

            $other = DB::table('purchases')
                            ->leftJoin('shops','shops.id','=','purchases.shop_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->sum('purchases.amount');   
                            
            $total_amount = 0;
            $labels = [];
            foreach ($shops as $key => $shop) {
                $labels[] = $shop->name;
                $toArray[] = [ 'x' => $shop->name , 'y' => (int) $shop->total_amount ];
                $total_amount += $shop->total_amount;
            }
            // $labels[] = 'others';
            // $toArray[] = [ 'x' => 'others' ,  'y' => $other - $total_amount ];


            $this->response['day_diff'] = $day_diff;
            $this->response['labels'] = $labels;
            $this->response['data'] = $toArray;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function saleByCategory(Request $request)
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

            $categories = DB::table('business_types')
                            ->select([
                                'business_types.*',
                                DB::raw("count(purchases.id) as total_amount")
                            ])
                            ->leftJoin('shops','shops.business_type_id','=','business_types.id')
                            ->leftJoin('purchases','shops.id','=','purchases.shop_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->where('purchases.status' , 1)
                            ->groupBy('purchases.shop_id')
                            ->orderBy('total_amount' , 'desc')
                            ->take(10)
                            ->get();         

            $other = DB::table('business_types')
                            ->leftJoin('shops','shops.business_type_id','=','business_types.id')
                            ->leftJoin('purchases','shops.id','=','purchases.shop_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->sum('purchases.amount');   
                            
            $total_amount = 0;
            $labels = [];
            foreach ($categories as $key => $category) {
                $labels[] = $category->name;
                $toArray[] = [ 'x' => $category->name ,  'y' => (int) $category->total_amount ];
                $total_amount += $category->total_amount;
            }
            // $labels[] = 'others';
            // $toArray[] = [ 'x' => 'others' ,  'y' => $other - $total_amount ];


            $this->response['day_diff'] = $day_diff;
            $this->response['labels'] = $labels;
            $this->response['data'] = $toArray;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function saleByPromoters(Request $request)
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

            $purchases = DB::table('users')
                            ->select([
                                'users.id',
                                DB::raw("CONCAT(`first_name`,' ', `last_name`) AS fullname"),
                                DB::raw("IFNULL(sum(purchases.amount),0) as total")
                            ])
                            ->leftJoin('purchases','users.id','=','purchases.user_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->where('purchases.status' , 1)
                            ->groupBy('users.id')
                            ->orderBy('total' , 'desc')
                            ->take(10)
                            ->get();         

            $other = DB::table('users')
                            ->leftJoin('purchases','users.id','=','purchases.user_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->sum('purchases.amount');   
                            
            $top_amount = 0;
            $labels = [];
            foreach ($purchases as $key => $purchase) {
                $labels[] = $purchase->fullname;
                $toArray[] = [ "x" => $purchase->fullname , "y" => (int) $purchase->total ];
                $top_amount += $purchase->total;
            }
            // $labels[] = 'others';
            // $toArray[] = [ "x" => 'others' , "y" => $other - $top_amount ];


            $this->response['day_diff'] = $day_diff;
            $this->response['labels'] = $labels;
            $this->response['data'] = $toArray;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function saleByCustomers(Request $request)
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

            $purchases = DB::table('customers')
                            ->select([
                                'customers.*',
                                DB::raw("CONCAT(`first_name`,' ', `last_name`) AS fullname"),
                                DB::raw("IFNULL(sum(purchases.amount),0) as total")
                            ])
                            ->leftJoin('purchases','customers.id','=','purchases.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->where('purchases.status' , 1)
                            ->groupBy('customers.id')
                            ->orderBy('total' , 'desc')
                            ->take(10)
                            ->get();         

            $other = DB::table('customers')
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
                $labels[] = $purchase->fullname;
                $toArray[] = [ "x" => $purchase->fullname , "y" => (int) $purchase->total ];
                $top_amount += $purchase->total;
            }
            // $labels[] = 'others';
            // $toArray[] = [ "x" => 'others' ,  "y" => $other - $top_amount ];


            $this->response['day_diff'] = $day_diff;
            $this->response['labels'] = $labels;
            $this->response['data'] = $toArray;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function sales(Request $request)
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

            $purchases = App\Purchase::with(["customer"])->where('status' , true)
                                        ->where(function($query) use($start_date , $end_date) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->whereBetween("created_at" ,  [ $start_date , $end_date ]);
                                            }                                                
                                        })
                                        ->orderBy('amount' , 'desc')
                                        ->take(10)
                                        ->get();    
                            
            $labels = [];
            foreach ($purchases as $key => $purchase) {
                $labels[] = $purchase->customer->fullname;
                $toArray[] = [  'x' => $purchase->customer->fullname , 'y' => (int) $purchase->amount ];
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

    public function campaign(Request $request)
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

            $campaigns = App\Campaign::where('status' , true)
                                        ->where(function($query) use($start_date , $end_date) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->whereBetween("start_at" ,  [ $start_date , $end_date ]);
                                            }                                                
                                        })
                                        ->orderBy('created_at' , 'desc')
                                        ->take(10)
                                        ->get(); 

            $labels = [];
            foreach ($campaigns as $key => $campaign) {

                $labels[] = $campaign->name;

                $amount = App\Purchase::where('status' , true)
                                        ->where(function($query) use($start_date , $end_date) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->whereBetween("created_at" ,  [ $start_date , $end_date ]);
                                            }                                                
                                        })
                                        ->where('status' , 1)
                                        ->sum("amount");
                                                        
                $toArray[] = [  'x' => $campaign->name , 'y' => (int) $amount ];
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
   
}