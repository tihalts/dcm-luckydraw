<?php 

namespace App\Http\Controllers;

use App;
use Lang;
use Config;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;


class HomeController extends Controller 
{

    var $response = [];
    var $filter = [
        "searchText" => "",
        "itemPerPage" => 15,
        "orderby" => "desc"
    ];

    public function __construct()
    {        
        
    }
    
    public function index()
    {
        $permissions = [];
        if(Auth::user()->role == 'admin'){
            return view('layout.admin');
        }
        return view('layout.promoter' , [ "user_role" => Auth::user()->role ]);
    }

    public function RaffleDraw($id)
    {
        $template = App\RaffleDrawSetting::where('lucky_draw_id' , $id)->first();
        $image =isset($template->image) ? asset('storage/' .$template->image) : null;
        if(Auth::user()->role == 'admin'){
            return view('layout.raffledraw' , ['image' => $image]);
        }
        return view('layout.promoter' , [ "user_role" => Auth::user()->role , 'image' => $image]);        
    }

    public function listCountries($page = 1)
    {     
        try {            
            $this->response['data'] = App\Country::where('status' , true)->get(["id" , "name" , "iso" , "phone_code"]);
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function getDashboardData()
    {
        try {            
            $toArray = []; 
            $current_month = Carbon::now()->month; 
            $report_from = Carbon::now()->subDays(30);         
            if(Auth::user()->role == 'admin'){
                $toArray['new_customers'] = App\Customer::where('status' , true)->whereDate('created_at' , '>' , $report_from)->count();
                $toArray['purchases'] = App\Purchase::where('status' , true)->whereDate('created_at' , '>' , $report_from)->count();
                $toArray['purchase_amount'] = App\Purchase::where('status' , true)->whereDate('created_at' , '>' , $report_from)->sum('amount');
                $toArray['last_month_purchase_amount'] = App\Purchase::where('status' , true)
                                                                       ->whereDate('created_at', '<=', $report_from)
                                                                       ->whereDate('created_at' , '>' , Carbon::now()->subDays(60))
                                                                       ->sum('amount');
            }else {
                $toArray['new_customers'] = App\Customer::where('created_user_id' , Auth::id())->where('status' , true)->whereDate('created_at' , '>' , $report_from)->count();
                $toArray['purchases'] = App\Purchase::where('user_id' , Auth::id())->where('status' , true)->whereDate('created_at' , '>' , $report_from)->count();
                $toArray['purchase_amount'] = App\Purchase::where('user_id' , Auth::id())->where('status' , true)->whereDate('created_at' , '>' , $report_from)->sum('amount');
                $toArray['last_month_purchase_amount'] = App\Purchase::where('user_id' , Auth::id())->where('status' , true)
                                                                       ->whereDate('created_at', '<=', $report_from)
                                                                       ->whereDate('created_at' , '>' , Carbon::now()->subDays(60))
                                                                       ->sum('amount');
            }
            
            $toArray['total_scratch_card_campaigns'] = App\Campaign::where('campaign_type' , 'scratch_card')
                                                                    ->where('status' , true)
                                                                    ->whereDate('start_at' , '>' , $report_from)
                                                                    ->count();

            $toArray['active_scratch_card_campaigns'] = App\Campaign::where('campaign_type' , 'scratch_card')
                                                                    ->where('status' , true)
                                                                    ->whereDate('start_at' , '<=' , Carbon::now())
                                                                    ->whereDate('end_at' , '>=' , Carbon::now())
                                                                    ->count();

            $toArray["totalGifts"] = App\Gift::where('status' , true)->whereDate('created_at' , '>' , $report_from)->count();

            $toArray["totalGiftItems"] = App\GiftItem::where('status' , true)
                                                        ->whereDate('gift_at' , '>' , $report_from)
                                                        ->count();

            $toArray["totalUnGiftItems"] = App\GiftItem::where('status' , true)
                                                        ->whereNull('gifted_at')
                                                        ->whereDate('gift_at' , '>' , $report_from)
                                                        ->count();

            $toArray["totalGiftedItems"] = App\GiftItem::where('status' , true)
                                                        ->whereNotNull('gifted_at')
                                                        ->whereDate('gifted_at' , '>' , $report_from)
                                                        ->count();

            $toArray["todayGiftedItems"] = App\GiftItem::where('status' , true)
                                                        ->whereDate('gift_at' , Carbon::now())
                                                        ->count();

            $toArray['total_voucher_campaigns'] = App\Campaign::where('campaign_type' , 'reward_point')
                                                                ->where('status' , true)
                                                                ->whereDate('start_at' , '>' , $report_from)
                                                                ->count();

            $toArray['active_voucher_campaigns'] = App\Campaign::where('campaign_type' , 'reward_point')
                                                                ->where('status' , true)
                                                                ->whereDate('start_at' , '<=' , Carbon::now())
                                                                ->whereDate('end_at' , '>=' , Carbon::now())
                                                                ->count();

            $toArray["vouchers"] = App\Voucher::where('status' , true)->whereDate('created_at' , '>' , $report_from)->count();
            $toArray["scratch_cards"] = App\ScratchCard::where('status' , true)->whereDate('created_at' , '>' , $report_from)->count();

            $this->response['data'] = $toArray;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
        
    }

    public function Activities(Request $request){
        try {      
            $filter = $request->all(); 
            $page = isset($filter['page']) ? $filter['page'] : 1 ;
            $activities = new Activity();

            if(isset($filter['user_id'])){
                $activities = $activities->where('causer_id' , $filter['user_id']);
            }

            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $activities = $activities->whereBetween('created_at' , [ $start_date , $end_date ]);
            }

            $this->response["totalItems"] = $activities->count();
            $this->response['data'] = $activities->with(['causer' , 'subject'])
                                                 ->orderBy('id' , isset($filter["orderby"]) ? $filter["orderby"] : 'desc')
                                                 ->offset(isset($filter["itemPerPage"]) ? $filter["itemPerPage"] * ($page - 1) : 15 * ($page - 1))
                                                 ->limit(isset($filter["itemPerPage"]) ? $filter["itemPerPage"] : 15)
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