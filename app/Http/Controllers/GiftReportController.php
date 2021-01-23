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


class GiftReportController extends Controller 
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

    public function customers(Request $request)
    {     
        try {  
            $toArray = []; 
            $purchases = DB::table('users')
                            ->select([
                                'users.id',
                                DB::raw("CONCAT(`first_name`,' ', `last_name`) AS fullname"),
                                DB::raw("IFNULL(sum(purchases.amount),0) as total")
                            ])
                            ->leftJoin('purchases','users.id','=','purchases.customer_id')
                            ->groupBy('users.id')
                            ->orderBy('total' , 'desc')
                            ->take(10)
                            ->get();         

            $other = DB::table('users')
                            ->leftJoin('purchases','users.id','=','purchases.customer_id')
                            ->sum('purchases.amount');   
                            
            $top_amount = 0;

            foreach ($purchases as $key => $purchase) {
                $toArray[] = [ "name" => $purchase->fullname , "amount" => $purchase->total ];
                $top_amount += $purchase->total;
            }

            $toArray[] = [ "name" => 'others' , "amount" => $other - $top_amount ];


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

    public function GiftReports(Request $request)
    {
        try {
            $filter = $request->all(); 
            $page = isset($filter['page']) ? $filter['page'] : 1;

            $gifts = App\Gift::where('status' , true);  
           

            if(isset($filter['campaign_id'])){
                $campaign = App\Campaign::find($filter['campaign_id']);

            } else {                
                $campaign = App\Campaign::where("campaign_type" , "scratch_card")->latest()->first();
               
            }

            if(isset($campaign)){
                $gifts = $gifts->where('campaign_id' , $campaign->id);
            } 

            $this->response["totalItems"] = $gifts->count();

            $gifts = $gifts->withCount(["Items as ungifted" => function($query) { $query->whereNull('gifted_at'); }]);
            $gifts = $gifts->withCount(["Items as gifted" => function($query) { $query->whereNotNull('gifted_at'); }]);

            $this->response['data'] = $gifts->orderBy('id' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
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


    public function giftedPromoters(Request $request)
    {
        # code...
    }
   
}