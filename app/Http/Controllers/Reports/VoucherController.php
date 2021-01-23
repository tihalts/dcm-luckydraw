<?php 

namespace App\Http\Controllers\Reports;

use App;
use PDF;
use Lang;
use Excel;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Exports\ReportExport;
use App\Exports\VouchersExport;
use App\Events\RewardPointVoucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;


class VoucherController extends Controller 
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

    public function list(Request $request)
    {
        try {      
            $filter = $request->all(); 
            $page = isset($filter['page']) ? $filter['page'] : 1;
            $vouchers = App\Voucher::where('status' , true);
            
            if(isset($filter['campaign_id'])){
                $vouchers = $vouchers->where('campaign_id' , $filter['campaign_id']);
            }

            if(isset($filter['customer_id'])){
                $vouchers = $vouchers->where('customer_id' , $filter['customer_id']);
            }

            if(isset($filter['user_id'])){
                $vouchers = $vouchers->where('user_id' , $filter['user_id']);
            }

            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $vouchers = $vouchers->whereBetween('created_at' ,[$start_date , $end_date]);
            }

            $this->response["totalItems"] = $vouchers->count();
            $this->response["totalAmount"] = $vouchers->sum('voucher_amount');
            $this->response['data'] = $vouchers->with(['Customer' , 'Campaign' , 'Provider'])
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
            $export = [];     
            $filter = $request->all(); 
            $vouchers = App\Voucher::where('status' , true);
            
            if(isset($filter['campaign_id'])){
                $vouchers = $vouchers->where('campaign_id' , $filter['campaign_id']);
            }

            if(isset($filter['customer_id'])){
                $vouchers = $vouchers->where('customer_id' , $filter['customer_id']);
            }

            if(isset($filter['user_id'])){
                $vouchers = $vouchers->where('user_id' , $filter['user_id']);
            }

            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $vouchers = $vouchers->whereBetween('created_at' ,[$start_date , $end_date]);
            }

            $vouchers = $vouchers->with(['Customer' , 'Campaign' , 'Provider'])
                                ->get()
                                ->toArray(); 

            foreach($vouchers as $key => $voucher){
                $export[$key]['Campaign Name'] = $voucher['campaign']['name'];
                $export[$key]['Customer Name'] = $voucher['customer']['fullname'];
                $export[$key]['Customer CPR'] = $voucher['customer']['cpr'];
                $export[$key]['Customer Email'] = $voucher['customer']['email'];
                $export[$key]['Customer mobile'] = $voucher['customer']['mobile'];
                $export[$key]['Voucher Code'] = $voucher['code'];
                $export[$key]['Voucher Amount'] = $voucher['voucher_amount'];
                $export[$key]['Provider By'] = isset($voucher['provider']['fullname']) ? $voucher['provider']['fullname'] : null;
                $export[$key]['Provider Role'] = isset($voucher['provider']['role']) ? $voucher['provider']['role'] : null;
                $export[$key]['Redeemed At'] = $voucher['redeemed_at'];
                $export[$key]['Created At'] = $voucher['created_at'];

            }

            return Excel::download(new VouchersExport($export), 'vouchers.xlsx');
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function export_pdf(Request $request , $type)
    {
        try {      
            $export = [];     
            $filter = $request->all(); 
            $vouchers = App\Voucher::where('status' , true);
            
            if(isset($filter['campaign_id'])){
                $vouchers = $vouchers->where('campaign_id' , $filter['campaign_id']);
            }

            if(isset($filter['customer_id'])){
                $vouchers = $vouchers->where('customer_id' , $filter['customer_id']);
            }

            if(isset($filter['user_id'])){
                $vouchers = $vouchers->where('user_id' , $filter['user_id']);
            }

            $start_date = isset($filter['start_date']) ? Carbon::parse($filter['start_date'])->startOfDay() : null;
            $end_date = isset($filter['end_date']) ?  Carbon::parse($filter['end_date'])->endOfDay() : Carbon::now()->endOfDay();

            if(!isset($start_date)){
                $start_date = Carbon::parse($end_date)->subDays(30)->startOfDay();
            }

            $vouchers = $vouchers->whereBetween('created_at' ,[$start_date , $end_date]);

            $vouchers = $vouchers->with(['Customer' , 'Campaign' , 'Provider'])
                                ->get()
                                ->toArray(); 

            foreach($vouchers as $key => $voucher){
                $export[$key]['Campaign Name'] = $voucher['campaign']['name'];
                $export[$key]['Customer Name'] = $voucher['customer']['fullname'];
                $export[$key]['Customer CPR'] = $voucher['customer']['cpr'];
                $export[$key]['Customer Email'] = $voucher['customer']['email'];
                $export[$key]['Customer mobile'] = $voucher['customer']['mobile'];
                $export[$key]['Voucher Code'] = $voucher['code'];
                $export[$key]['Voucher Amount'] = $voucher['voucher_amount'];
                $export[$key]['Provider By'] = isset($voucher['provider']['fullname']) ? $voucher['provider']['fullname'] : null;
                $export[$key]['Provider Role'] = isset($voucher['provider']['role']) ? $voucher['provider']['role'] : null;
                $export[$key]['Redeemed At'] = $voucher['redeemed_at'];
                $export[$key]['Created At'] = $voucher['created_at'];

            }

            
            if($type == 'pdf'){
                $pdf = PDF::loadView('exports.voucher' , ['vouchers' => $export ]);
                return $pdf->download('vouchers.pdf');
            }else {
                return Excel::download(new ReportExport($export), Uuid::uuid1(). '.xlsx');
            }
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }


    public function line_chart(Request $request)
    {
        $filter = $request->all(); 
        $page = isset($filter['page']) ? $filter['page'] : 1;
        $vouchers = App\Voucher::where('status' , true);
        
        if(isset($filter['campaign_id'])){
            $vouchers = $vouchers->where('campaign_id' , $filter['campaign_id']);
        }

        if(isset($filter['customer_id'])){
            $vouchers = $vouchers->where('customer_id' , $filter['customer_id']);
        }

        if(isset($filter['user_id'])){
            $vouchers = $vouchers->where('user_id' , $filter['user_id']);
        }

        if(isset($filter['start_date']) && isset($filter['end_date'])){
            $start_date = Carbon::parse($filter['start_date'])->startOfDay();
            $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            $vouchers = $vouchers->whereBetween('created_at' ,[$start_date , $end_date]);
        }else{
            $vouchers = $vouchers->whereBetween('created_at' ,[ now()->subDays(30) , now() ]);
        }

        $vouchers = $vouchers->select([ DB::Raw('count(*) as count') , DB::Raw('DATE(created_at) day') ])
                                ->groupBy('day')
                                ->orderBy('day')
                                ->get()
                                ->pluck('count' , 'day'); 


        $this->response['data'] = $vouchers;
        return $this->successResponse($this->response);
    }

    public function voucherByPromoters(Request $request)
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

            $vouchers = App\User::withCount(["vouchers as total_vouchers" => function($query) use($start_date , $end_date) {
                                    if(isset($start_date) && isset($end_date)){
                                        $query->whereBetween("created_at" , [ $start_date , $end_date ]);
                                    }
                                }]) 
                                ->orderBy('total_vouchers' , 'desc')  
                                ->take(10)
                                ->get();   
                            
            //$total_vouchers = 0;
            $labels = [];
            foreach ($vouchers as $key => $voucher) {
                $labels[] = $voucher->fullname;
                $toArray[] = [ "x" => $voucher->fullname ,  "y" => (int) $voucher->total_vouchers ];
            }

            //$toArray[] = [ 'others' ,  $other - $total_vouchers ];

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