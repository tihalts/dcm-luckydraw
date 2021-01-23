<?php 

namespace App\Http\Controllers\Reports;

use PDF;
use App;
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
use Illuminate\Database\Eloquent\Builder;
use Ramsey\Uuid\Uuid;

class PurchaseListController extends Controller 
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

    public function Campaigns(Request $request)
    {
        try { 

            $filter = $request->all(); 
            $page = isset($filter['page']) ? $filter['page'] : 1;

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
                                        });
                           

            $this->response["totalItems"] = $campaigns->count();

            $campaigns = $campaigns->orderBy('created_at' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                    ->offset(15 * ($page - 1))
                                    ->limit(15)
                                    ->get(); 

            foreach ($campaigns as $key => $campaign) {
                $campaign->purchase_amount = App\Purchase::where('status' , true)
                                                            ->where(function($query) use($start_date , $end_date) {
                                                                if(isset($start_date) && isset($end_date)){
                                                                    $query->whereBetween("created_at" ,  [ $start_date , $end_date ]);
                                                                }                                                
                                                            })
                                                            ->sum("amount");
            }

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $campaigns;
            return $this->successResponse($this->response);

        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function export_campaign_pdf(Request $request, $type)
    {
        $filter = $request->all();
        $page = isset($filter['page']) ? $filter['page'] : 1;


        $start_date = null;
        $end_date = null;
        $day_diff = 0;

        if(isset($filter['start_date']) && isset($filter['end_date'])){
            $start_date = Carbon::parse($filter['start_date'])->startOfDay();
            $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            $day_diff = $end_date->diffInDays($start_date);
            
        } 

        $campaigns = App\Campaign::where('status' , true);
                           

        $this->response["totalItems"] = $campaigns->count();

        $campaigns = $campaigns->orderBy('created_at' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                ->offset(15 * ($page - 1))
                                ->limit(15)
                                ->get(); 

        foreach ($campaigns as $key => $campaign) {
            $campaign->purchase_amount = App\Purchase::where('status' , true)
                                                        ->whereBetween('created_at' , [ $campaign->start_at , $campaign->end_at ])
                                                        ->sum("amount");
        }

        $data = $campaigns;
        
        if($type == 'pdf'){           
            $pdf = PDF::loadView('exports.purchases.campaigns', ["data" => $data]);
            return $pdf->download('campagin_sales.pdf');
        }else {
            return Excel::download(new ReportExport($data->toArray()), Uuid::uuid1(). '.xlsx');
        }
    }

    public function Days(Request $request)
    {
        try {  

            $filter = $request->all();
            $page = isset($filter['page']) ? $filter['page'] : 1;


            $start_date = null;
            $end_date = null;
            $day_diff = 0;

            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $day_diff = $end_date->diffInDays($start_date);
                
            } 

            $purchases = Purchase::where('status' , true)
                                    ->select([
                                        DB::raw('DATE(created_at) as date'),
                                        DB::raw("sum(amount) as total_amount")
                                    ])
                                    ->where(function($query) use($start_date , $end_date) {
                                        if(isset($start_date) && isset($end_date)){
                                            $query->whereBetween("created_at" ,  [ $start_date , $end_date ]);
                                        }                                                
                                    })
                                    ->where('status' , 1)
                                    ->groupBy(DB::raw('Date(created_at)'));

            $this->response["totalItems"] = $purchases->count();

            $purchases = $purchases->orderBy('total_amount' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                    ->offset(15 * ($page - 1))
                                    ->limit(15)
                                    ->get(); 

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $purchases;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function export_day_pdf(Request $request, $type)
    {
        $filter = $request->all();
        $page = isset($filter['page']) ? $filter['page'] : 1;


        $start_date = null;
        $end_date = null;
        $day_diff = 0;

        if(isset($filter['start_date']) && isset($filter['end_date'])){
            $start_date = Carbon::parse($filter['start_date'])->startOfDay();
            $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            $day_diff = $end_date->diffInDays($start_date);
            
        } 

        $purchases = Purchase::where('status' , true)
                                ->select([
                                    DB::raw('DATE(created_at) as date'),
                                    DB::raw("sum(amount) as total_amount")
                                ])
                                ->where(function($query) use($start_date , $end_date) {
                                    if(isset($start_date) && isset($end_date)){
                                        $query->whereBetween("created_at" ,  [ $start_date , $end_date ]);
                                    }                                                
                                })
                                ->where('status' , 1)
                                ->groupBy(DB::raw('Date(created_at)'))
                                ->orderBy('total_amount' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                ->get();

        $data = $purchases;
       
        if($type == 'pdf'){           
            $pdf = PDF::loadView('exports.purchases.days', ["data" => $data]);
            return $pdf->download('day_sales.pdf');
        }else {
            return Excel::download(new ReportExport($data->toArray()), Uuid::uuid1(). '.xlsx');
        }
    }

    public function Shops(Request $request)
    {
        try {  
            $filter = $request->all();
            $page = isset($filter['page']) ? $filter['page'] : 1;

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
                                DB::raw("sum(purchases.amount) as total_amount")
                            ])
                            ->Join('purchases','shops.id','=','purchases.shop_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->where('purchases.status' , 1)
                            ->groupBy('purchases.shop_id');

            $this->response["totalItems"] = $shops->count();

            $shops = $shops->orderBy('total_amount' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                    ->offset(15 * ($page - 1))
                                    ->limit(15)
                                    ->get(); 

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $shops;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function export_shop_pdf(Request $request, $type)
    {
        $filter = $request->all();
        $page = isset($filter['page']) ? $filter['page'] : 1;


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
                            DB::raw("sum(purchases.amount) as total_amount")
                        ])
                        ->Join('purchases','shops.id','=','purchases.shop_id')
                        ->where(function($query) use($start_date , $end_date) {
                            if(isset($start_date) && isset($end_date)){
                                $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                            }                                                
                        })
                        ->where('purchases.status' , 1)
                        ->groupBy('purchases.shop_id')
                        ->orderBy('total_amount' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                        ->get(); 

        $data = $shops;
       
        if($type == 'pdf'){           
            $pdf = PDF::loadView('exports.purchases.shops', ["data" => $data]);
            return $pdf->download('shop_sales.pdf');
        }else {
            return Excel::download(new ReportExport($data->toArray()), Uuid::uuid1(). '.xlsx');
        }
    }

    public function Categories(Request $request)
    {
        try { 

            $filter = $request->all();
            $page = isset($filter['page']) ? $filter['page'] : 1;

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
                                DB::raw("sum(purchases.amount) as total_amount")
                            ])
                            ->Join('shops','shops.business_type_id','=','business_types.id')
                            ->Join('purchases','shops.id','=','purchases.shop_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->where('purchases.status' , 1)
                            ->groupBy('purchases.shop_id');  

            $this->response["totalItems"] = $categories->count("business_types.id");

            $categories = $categories->orderBy('total_amount' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                    ->offset(15 * ($page - 1))
                                    ->limit(15)
                                    ->get(); 
            
            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $categories;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function export_category_pdf(Request $request, $type)
    {
        $filter = $request->all();
        $page = isset($filter['page']) ? $filter['page'] : 1;


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
                                DB::raw("sum(purchases.amount) as total_amount")
                            ])
                            ->Join('shops','shops.business_type_id','=','business_types.id')
                            ->Join('purchases','shops.id','=','purchases.shop_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->where('purchases.status' , 1)
                            ->groupBy('purchases.shop_id')
                            ->orderBy('total_amount' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                            ->get();  

        $data = $categories;
        
        if($type == 'pdf'){           
            $pdf = PDF::loadView('exports.purchases.categories', ["data" => $data]);
            return $pdf->download('category_sales.pdf');
        }else {
            return Excel::download(new ReportExport($data->toArray()), Uuid::uuid1(). '.xlsx');
        }
    }

    public function Promoters(Request $request)
    {     
        try {  
            $filter = $request->all();
            $page = isset($filter['page']) ? $filter['page'] : 1;

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
                                'users.*',
                                DB::raw("CONCAT(`first_name`,' ', `last_name`) AS fullname"),
                                DB::raw("IFNULL(sum(purchases.amount),0) as sale_amount")
                            ])
                            ->Join('purchases','users.id','=','purchases.user_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->where('purchases.status' , 1)
                            ->groupBy('users.id'); 

            $this->response["totalItems"] = DB::table('users')->count();

            $purchases = $purchases->orderBy('sale_amount' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                    ->offset(15 * ($page - 1))
                                    ->limit(15)
                                    ->get(); 

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $purchases;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function export_promoter_pdf(Request $request, $type)
    {
        $filter = $request->all();
        $page = isset($filter['page']) ? $filter['page'] : 1;


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
                            'users.*',
                            DB::raw("CONCAT(`first_name`,' ', `last_name`) AS fullname"),
                            DB::raw("IFNULL(sum(purchases.amount),0) as sale_amount")
                        ])
                        ->Join('purchases','users.id','=','purchases.user_id')
                        ->where(function($query) use($start_date , $end_date) {
                            if(isset($start_date) && isset($end_date)){
                                $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                            }                                                
                        })
                        ->where('purchases.status' , 1)
                        ->groupBy('users.id')
                        ->orderBy('sale_amount' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                        ->get();  

        $data = $purchases;
       
        if($type == 'pdf'){         
            $pdf = PDF::loadView('exports.purchases.promoters', ["data" => $data]);
            return $pdf->download('promoter_sales.pdf');
        }else {
            return Excel::download(new ReportExport($data->toArray()), Uuid::uuid1(). '.xlsx');
        }
    }

    public function Customers(Request $request)
    {     
        try {  
            $filter = $request->all();
            $page = isset($filter['page']) ? $filter['page'] : 1;

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
                                DB::raw("IFNULL(sum(purchases.amount),0) as sale_amount")
                            ])
                            ->leftJoin('purchases','customers.id','=','purchases.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->where('purchases.status' , 1)
                            ->groupBy('customers.id');    

            $this->response["totalItems"] = $purchases->count();

            $purchases = $purchases->orderBy('sale_amount' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                    ->offset(15 * ($page - 1))
                                    ->limit(15)
                                    ->get(); 

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $purchases;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function export_customer_pdf(Request $request, $type)
    {
        $filter = $request->all();
        $page = isset($filter['page']) ? $filter['page'] : 1;


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
                                DB::raw("IFNULL(sum(purchases.amount),0) as sale_amount")
                            ])
                            ->leftJoin('purchases','customers.id','=','purchases.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->where('purchases.status' , 1)
                            ->groupBy('customers.id')
                            ->orderBy('sale_amount' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                            ->take(100)
                            ->get();  

        $data = $purchases;
       
        if($type == 'pdf'){           
            $pdf = PDF::loadView('exports.purchases.customers', ["data" => $data]);
            return $pdf->download('customer_sales.pdf');
        }else {
            return Excel::download(new ReportExport($data->toArray()), Uuid::uuid1(). '.xlsx');
        }
    }

    public function Countries(Request $request)
    {     
        try {  
            $filter = $request->all();
            $page = isset($filter['page']) ? $filter['page'] : 1;

            $start_date = null;
            $end_date = null;
            $day_diff = 0;

            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $day_diff = $end_date->diffInDays($start_date);
            } 

            $countries = DB::table('countries')
                            ->select([
                                'countries.*',
                                DB::raw("IFNULL(sum(purchases.amount),0) as total_amount")
                            ])
                            ->leftJoin('customers', 'customers.country_iso', '=' , 'countries.iso')
                            ->leftJoin('purchases','customers.id','=','purchases.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->where('purchases.status' , 1)
                            ->groupBy('customers.country_iso'); 

            $this->response["totalItems"] = $countries->count();

            $countries = $countries->orderBy('total_amount' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                    ->offset(15 * ($page - 1))
                                    ->limit(15)
                                    ->get(); 

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $countries;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function export_country_pdf(Request $request, $type)
    {
        $filter = $request->all();
        $page = isset($filter['page']) ? $filter['page'] : 1;


        $start_date = null;
        $end_date = null;
        $day_diff = 0;

        if(isset($filter['start_date']) && isset($filter['end_date'])){
            $start_date = Carbon::parse($filter['start_date'])->startOfDay();
            $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            $day_diff = $end_date->diffInDays($start_date);
            
        } 

        $countries = DB::table('countries')
                            ->select([
                                'countries.*',
                                DB::raw("IFNULL(sum(purchases.amount),0) as total_amount")
                            ])
                            ->leftJoin('customers', 'customers.country_iso', '=' , 'countries.iso')
                            ->leftJoin('purchases','customers.id','=','purchases.customer_id')
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->where('purchases.status' , 1)
                            ->groupBy('customers.country_iso')
                            ->orderBy('total_amount' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                            ->get(); 

        $data = $countries;
       
        if($type == 'pdf'){         
            $pdf = PDF::loadView('exports.purchases.countries', ["data" => $data]);
            return $pdf->download('country_sales.pdf');
        }else {
            return Excel::download(new ReportExport($data->toArray()), Uuid::uuid1(). '.xlsx');
        }
    }
}