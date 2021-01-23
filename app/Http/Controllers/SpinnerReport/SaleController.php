<?php 

namespace App\Http\Controllers\SpinnerReport;

use App;
use PDF;
use Lang;
use Excel;
use Config;
use Carbon\Carbon;
use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Events\ScratchCardWinner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Exports\SpinnerSaleReportExport;
use Illuminate\Support\Facades\Validator;


class SaleController extends Controller
{
    public function spinners(Request $request)
    {
        try {      
            $filter = $request->all(); 
            $spinwins = App\Spinner::where('status' , true);

            if(isset($filter['searchText'])){
                $spinwins = $spinwins->where(function($query) use($filter){
                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }  
            $this->response['data'] = $spinwins->get(['id' , 'name']);
           
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function index(Request $request, $page = 1)
    {
        try {
            $spinner = $request->has('spinner_id') ? App\Spinner::find($request->spinner_id) : App\Spinner::first();

            if(!isset($spinner)){
                return $this->errorResponse($this->response);
            }
            $day_diff = 0;

            $filter = $request->all();

            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start = Carbon::parse($filter['start_date'])->format('Y-m-d h:i:s');
                $end = Carbon::parse($filter['end_date'])->format('Y-m-d h:i:s');

                if($start >= $spinner->start_at && $start <= $spinner->end_at){
                    $start_date = Carbon::parse($filter['start_date'])->startOfDay()->format('Y-m-d h:i:s');
                }else{
                    $start_date = $spinner->start_at;
                }

                if($end >= $spinner->start_at && $end <= $spinner->end_at){
                    $end_date = Carbon::parse($filter['end_date'])->startOfDay()->format('Y-m-d h:i:s');
                }else{
                    $end_date = $spinner->end_at;
                }

               // $day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));
            } else{
                $start_date = $spinner->start_at;
                $end_date = $spinner->end_at;
            }

            $day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));

            //return [$start_date , $end_date];

            $purchases = App\Purchase::where('status', true)
                                        ->whereBetween("created_at" ,  [ $start_date , $end_date ]);

            if($day_diff <= 30){
                $purchases = $purchases->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as date , sum(amount) as amount ,  COUNT(*) as total");
            } else if($day_diff > 30 && $day_diff <= 365){
                $purchases = $purchases->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as date , sum(amount) as amount ,  COUNT(*) as total");
            } else if($day_diff > 365){
                $purchases = $purchases->selectRaw("DATE_FORMAT(created_at, '%Y') as date , sum(amount) as amount ,  COUNT(*) as total");
            } 

            $this->response["totalItems"] = App\Purchase::where('status', true)
                                                        ->whereBetween("created_at" ,  [ $start_date , $end_date ])
                                                        ->selectRaw("date(created_at) ,  COUNT(*) as total")
                                                        ->groupBy(DB::Raw("date(created_at)"))
                                                        ->get()
                                                        ->count();

            $this->response['data'] = $purchases->offset(15 * ($page - 1))
                                                ->limit(15)
                                                ->groupBy(DB::Raw('date'))
                                                ->orderBy('date' , isset($filter["orderby"]) ? $filter["orderby"] : 'desc')
                                                ->get();

            $this->response["currentPage"] = $page;
            return $this->successResponse($this->response);
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function totalReport(Request $request)
    {
        try {
            $spinner = $request->has('spinner_id') ? App\Spinner::find($request->spinner_id) : App\Spinner::first();

            if(!isset($spinner)){
                return $this->errorResponse($this->response);
            }
            $day_diff = 0;

            $filter = $request->all();

            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start = Carbon::parse($filter['start_date'])->format('Y-m-d h:i:s');
                $end = Carbon::parse($filter['end_date'])->format('Y-m-d h:i:s');

                if($start >= $spinner->start_at && $start <= $spinner->end_at){
                    $start_date = Carbon::parse($filter['start_date'])->startOfDay()->format('Y-m-d h:i:s');
                }else{
                    $start_date = $spinner->start_at;
                }

                if($end >= $spinner->start_at && $end <= $spinner->end_at){
                    $end_date = Carbon::parse($filter['end_date'])->startOfDay()->format('Y-m-d h:i:s');
                }else{
                    $end_date = $spinner->end_at;
                }

               
            } else{
                $start_date = $spinner->start_at;
                $end_date = $spinner->end_at;
            }

            $day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));

            $this->response['data']["totalSales"] =  App\Purchase::where("purchases.status", 1)
                                                                    ->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ])
                                                                    ->whereHas('Customer.SpinWinners', function ($query) use ($spinner) {
                                                                        $query->where("spinner_id", $spinner->id)
                                                                            ->where("status", 1);
                                                                    })
                                                                    ->sum('purchases.amount');

            $this->response['data']["totalPurchases"] =  App\Purchase::where("purchases.status", 1)
                                                                        ->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ])
                                                                        ->whereHas('Customer.SpinWinners', function ($query) use ($spinner) {
                                                                            $query->where("spinner_id", $spinner->id)
                                                                                ->where("status", 1);
                                                                        })
                                                                        ->count();

            $this->response['data']["totalGifts"] = App\SpinWinner::where('status', true)
                                                                    //->whereHas('GiftCode')
                                                                    ->whereNotNull('code')
                                                                    ->where("spinner_id" ,  $spinner->id)
                                                                    //->where('is_gifted' , true)
                                                                    ->whereBetween("created_at" ,  [ $start_date , $end_date ])
                                                                    ->count();
           
            return $this->successResponse($this->response);
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function pdf(Request $request)
    {
        try {
            $spinner = $request->has('spinner_id') ? App\Spinner::find($request->spinner_id) : App\Spinner::first();

            if(!isset($spinner)){
                return $this->errorResponse($this->response);
            }
            $day_diff = 0;

            $filter = $request->all();

            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start = Carbon::parse($filter['start_date'])->format('Y-m-d h:i:s');
                $end = Carbon::parse($filter['end_date'])->format('Y-m-d h:i:s');

                if($start >= $spinner->start_at && $start <= $spinner->end_at){
                    $start_date = Carbon::parse($filter['start_date'])->startOfDay()->format('Y-m-d h:i:s');
                }else{
                    $start_date = $spinner->start_at;
                }

                if($end >= $spinner->start_at && $end <= $spinner->end_at){
                    $end_date = Carbon::parse($filter['end_date'])->startOfDay()->format('Y-m-d h:i:s');
                }else{
                    $end_date = $spinner->end_at;
                }

               // $day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));
            } else{
                $start_date = $spinner->start_at;
                $end_date = $spinner->end_at;
            }

            $day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));

            //return [$start_date , $end_date];

            $purchases = App\Purchase::where('status', true)
                                        ->whereBetween("created_at" ,  [ $start_date , $end_date ]);

            if($day_diff <= 30){
                $purchases = $purchases->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as date , sum(amount) as amount ,  COUNT(*) as total");
            } else if($day_diff > 30 && $day_diff <= 365){
                $purchases = $purchases->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as date , sum(amount) as amount ,  COUNT(*) as total");
            } else if($day_diff > 365){
                $purchases = $purchases->selectRaw("DATE_FORMAT(created_at, '%Y') as date , sum(amount) as amount ,  COUNT(*) as total");
            } 

            $purchases = $purchases->groupBy(DB::Raw('date'))->get();

            //$headers = isset($purchases[0]) ? array_keys($purchases[0]) : [];
            $pdf = PDF::loadView('exports.spinner.sales' , [ 'purchases' => $purchases ]);
            return $pdf->download('spinner_sales.pdf');
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function exports(Request $request)
    {
        try {
            $spinner = $request->has('spinner_id') ? App\Spinner::find($request->spinner_id) : App\Spinner::first();

            if(!isset($spinner)){
                return $this->errorResponse($this->response);
            }
            $day_diff = 0;

            $filter = $request->all();

            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start = Carbon::parse($filter['start_date'])->format('Y-m-d h:i:s');
                $end = Carbon::parse($filter['end_date'])->format('Y-m-d h:i:s');

                if($start >= $spinner->start_at && $start <= $spinner->end_at){
                    $start_date = Carbon::parse($filter['start_date'])->startOfDay()->format('Y-m-d h:i:s');
                }else{
                    $start_date = $spinner->start_at;
                }

                if($end >= $spinner->start_at && $end <= $spinner->end_at){
                    $end_date = Carbon::parse($filter['end_date'])->startOfDay()->format('Y-m-d h:i:s');
                }else{
                    $end_date = $spinner->end_at;
                }

               // $day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));
            } else{
                $start_date = $spinner->start_at;
                $end_date = $spinner->end_at;
            }

            $day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));

            //return [$start_date , $end_date];

            $purchases = App\Purchase::where('status', true)
                                        ->whereBetween("created_at" ,  [ $start_date , $end_date ]);

            if($day_diff <= 30){
                $purchases = $purchases->selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as date , sum(amount) as amount ,  COUNT(*) as total");
            } else if($day_diff > 30 && $day_diff <= 365){
                $purchases = $purchases->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as date , sum(amount) as amount ,  COUNT(*) as total");
            } else if($day_diff > 365){
                $purchases = $purchases->selectRaw("DATE_FORMAT(created_at, '%Y') as date , sum(amount) as amount ,  COUNT(*) as total");
            } 

            $purchases = $purchases->groupBy(DB::Raw('date'))->get()->toArray();

            //$headers = isset($purchases[0]) ? array_keys($purchases[0]) : [];
            return Excel::download(new SpinnerSaleReportExport($purchases), 'spin-and-win-sales-report.xlsx'); 

        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }
}