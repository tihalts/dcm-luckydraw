<?php 

namespace App\Http\Controllers\Reports;

use PDF;
use App;
use Lang;
use Excel;
use App\Voucher;
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

class VoucherListController extends Controller 
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
                                      ->whereHas('Vouchers' , function (Builder $query) use( $start_date , $end_date ) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->where("created_at" , [ $start_date , $end_date ]);
                                            }
                                      })
                                      ->withCount(["Vouchers as total_vouchers" => function (Builder $query) use( $start_date , $end_date ) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->where("created_at" , [ $start_date , $end_date ]);
                                            }
                                        }]);
                           

            $this->response["totalItems"] = $campaigns->count();

            $campaigns = $campaigns->orderBy('total_vouchers' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                    ->offset(15 * ($page - 1))
                                    ->limit(15)
                                    ->get(); 

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $campaigns;
            return $this->successResponse($this->response);

        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function export_campaign_pdf(Request $request , $type)
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

        $campaigns = App\Campaign::where('status' , true)
                                    ->whereHas('Vouchers' , function (Builder $query) use( $start_date , $end_date ) {
                                        if(isset($start_date) && isset($end_date)){
                                            $query->where("created_at" , [ $start_date , $end_date ]);
                                        }
                                    })
                                    ->withCount(["Vouchers as total_vouchers" => function (Builder $query) use( $start_date , $end_date ) {
                                        if(isset($start_date) && isset($end_date)){
                                            $query->where("created_at" , [ $start_date , $end_date ]);
                                        }
                                    }])
                                    ->orderBy('total_vouchers' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                    ->get(); 

        $data = $campaigns;
       
        if($type == 'pdf'){      
            $pdf = PDF::loadView('exports.vouchers.campaigns', ["data" => $data]);
            return $pdf->download('campaign_vouchers.pdf');
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

            $vouchers = Voucher::where('status' , true)
                                ->with("Campaign:id,name")
                                ->select([
                                    "campaign_id" ,
                                    DB::raw('DATE(created_at) as date'),
                                    DB::raw("count(vouchers.id) as total_vouchers")
                                ])
                                ->where(function($query) use($start_date , $end_date) {
                                    if(isset($start_date) && isset($end_date)){
                                        $query->whereBetween("created_at" ,  [ $start_date , $end_date ]);
                                    }                                                
                                })
                                ->groupBy(DB::raw('Date(created_at)'));

            $this->response["totalItems"] = $vouchers->count();

            $vouchers = $vouchers->orderBy('date' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                    ->offset(15 * ($page - 1))
                                    ->limit(15)
                                    ->get(); 

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $vouchers;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function export_days_pdf(Request $request , $type)
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

        $vouchers = Voucher::where('status' , true)
                            ->with("Campaign:id,name")
                            ->select([
                                "campaign_id" ,
                                DB::raw('DATE(created_at) as date'),
                                DB::raw("count(vouchers.id) as total_vouchers")
                            ])
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->groupBy(DB::raw('Date(created_at)'))
                            ->orderBy('date' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                            ->get();  

        $data = $vouchers;
        
        if($type == 'pdf'){         
            $pdf = PDF::loadView('exports.vouchers.days', ["data" => $data]);
            return $pdf->download('vouchers.pdf');
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

            
            $promoters = App\User::where('status' , true)
                                      ->whereHas('Vouchers' , function (Builder $query) use( $start_date , $end_date ) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->where("created_at" , [ $start_date , $end_date ]);
                                            }
                                      })
                                      ->withCount(["Vouchers as total_vouchers" => function (Builder $query) use( $start_date , $end_date ) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->where("created_at" , [ $start_date , $end_date ]);
                                            }
                                        }]);
                           

            $this->response["totalItems"] = $promoters->count();

            $promoters = $promoters->orderBy('total_vouchers' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                    ->offset(15 * ($page - 1))
                                    ->limit(15)
                                    ->get(); 

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $promoters;
            return $this->successResponse($this->response);

        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function export_promoter_pdf(Request $request , $type)
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

        $promoters = App\User::where('status' , true)
                                ->whereHas('Vouchers' , function (Builder $query) use( $start_date , $end_date ) {
                                    if(isset($start_date) && isset($end_date)){
                                        $query->where("created_at" , [ $start_date , $end_date ]);
                                    }
                                })
                                ->withCount(["Vouchers as total_vouchers" => function (Builder $query) use( $start_date , $end_date ) {
                                    if(isset($start_date) && isset($end_date)){
                                        $query->where("created_at" , [ $start_date , $end_date ]);
                                    }
                                }])
                                ->orderBy('total_vouchers' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                ->get();

        $data = $promoters;
        
        if($type == 'pdf'){         
            $pdf = PDF::loadView('exports.vouchers.promoters', ["data" => $data]);
            return $pdf->download('promoter_vouchers.pdf');
        }else {
            return Excel::download(new ReportExport($data->toArray()), Uuid::uuid1(). '.xlsx');
        }
    }
}