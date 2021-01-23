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
use App\Exports\ReportExport;
use App\Events\ScratchCardWinner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Exports\SpinnerCustomerReportExport;
use Ramsey\Uuid\Uuid;

class CustomerController extends Controller
{

    public function index(Request $request , $page = 1)
    {
        try {
            $spinner = $request->has('spinner_id') ? App\Spinner::find($request->spinner_id) : App\Spinner::first();

            if(!isset($spinner)){
                return $this->errorResponse($this->response);
            }
            $day_diff = 0;

            $filter = $request->all();

            
            //return [$start_date , $end_date];
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

                //$day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));
            } else{
                $start_date = $spinner->start_at;
                $end_date = $spinner->end_at;
            }

            $day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));

            //return [$start_date , $end_date];

            //return [$start_date , $end_date];
            $purchases = DB::table('purchases')->join('customers' , 'purchases.customer_id' , '='  , 'customers.id')
                                               ->whereExists(function ($query) use($start_date , $end_date) {
                                                    $query->select("spin_winners.id")
                                                        ->from('spin_winners')
                                                        ->whereRaw('customers.id = spin_winners.customer_id')
                                                        ->whereBetween('spin_winners.created_at' , [$start_date , $end_date]);
                                                })
                                                ->whereBetween('purchases.created_at' , [$start_date , $end_date]);

            if($request->type == 'new'){
                $purchases = $purchases->where('customers.created_at' , '>=' , $spinner->start_at);
            }else if($request->type == 'old'){
                $purchases = $purchases->where('customers.created_at' , '<=' , $spinner->start_at);
            }


            // $customers = App\Purchase::where('status', true)
            //                             ->whereHas('customers' , function($query) use($start_date , $end_date){
            //                                 $query->whereBetween("created_at" ,  [ $start_date , $end_date ]);
            //                             })
            //                             //->addSelect(DB::raw('sum(purchases.amount) as amount'))
                                       
            //                             ->whereHas('customers.SpinWinners', function ($query) use ($spinner) {
            //                                 $query->where("spinner_id", $spinner->id)
            //                                       ->where("status", 1);
            //                             });

            if($day_diff <= 30){
                $purchases = $purchases->selectRaw("DATE_FORMAT(purchases.created_at, '%Y-%m-%d') as date , sum(purchases.amount) as amount,  COUNT(DISTINCT customers.id) as total")
                                       ->groupBy(DB::Raw('date'));
            } else if($day_diff > 30 && $day_diff <= 365){
                $purchases = $purchases->selectRaw("DATE_FORMAT(purchases.created_at, '%Y-%m') as date , sum(purchases.amount) as amount,  COUNT(DISTINCT customer_id) as total")
                                       ->groupBy(DB::Raw('date'));
            } else if($day_diff > 365){
                $purchases = $purchases->selectRaw("DATE_FORMAT(purchases.created_at, '%Y') as date , sum(purchases.amount) as amount,   COUNT(DISTINCT customer_id) as total")
                                       ->groupBy(DB::Raw('date'));
            }

            $purchase_count = DB::table('purchases')->join('customers' , 'purchases.customer_id' , '='  , 'customers.id')
                                                    ->whereExists(function ($query) use($start_date , $end_date) {
                                                        $query->select("spin_winners.id")
                                                            ->from('spin_winners')
                                                            ->whereRaw('customers.id = spin_winners.customer_id')
                                                            ->whereBetween('spin_winners.created_at' , [$start_date , $end_date]);
                                                    })
                                                    ->whereBetween('purchases.created_at' , [$start_date , $end_date]);
            
            if($request->type == 'new'){
                $purchase_count = $purchase_count->whereBetween('customers.created_at' , '>=' , [$spinner->start_at , $spinner->end_at]);
            }else if($request->type == 'old'){
                $purchase_count = $purchase_count->where('customers.created_at' , '<=' , $spinner->start_at);
            }

            if($day_diff <= 30){
                $purchase_count = $purchase_count->selectRaw("DATE_FORMAT(purchases.created_at, '%Y-%m-%d') as date , COUNT(*) as purchases")
                                       ->groupBy(DB::Raw('date'));
            } else if($day_diff > 30 && $day_diff <= 365){
                $purchase_count = $purchase_count->selectRaw("DATE_FORMAT(purchases.created_at, '%Y-%m') as date ,  COUNT(*) as purchases")
                                       ->groupBy(DB::Raw('date'));
            } else if($day_diff > 365){
                $purchase_count = $purchase_count->selectRaw("DATE_FORMAT(purchases.created_at, '%Y') as date , COUNT(*) as purchases")
                                       ->groupBy(DB::Raw('date'));
            }

            //return $purchases->toSql();

            $this->response["totalItems"] = $purchase_count->get()->count('date');

            $this->response['data'] = $purchases->offset(15 * ($page - 1))
                                                ->limit(15)
                                                //->groupBy(DB::Raw('date'))
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

                //$day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));
            } else{
                $start_date = $spinner->start_at;
                $end_date = $spinner->end_at;
            }
            $day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));

            //return [$start_date , $end_date];

            $this->response['data']["totalSales"] =  App\Purchase::where("purchases.status", 1)
                                                        ->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ])
                                                        ->whereHas('Customer.SpinWinners', function ($query) use ($spinner) {
                                                            $query->where("spinner_id", $spinner->id)
                                                                  ->where("status", 1);
                                                        })
                                                        ->sum('purchases.amount');

            $this->response['data']["totalNewCustomerSales"] =  App\Purchase::where("purchases.status", 1)
                                                                                ->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ])
                                                                                ->whereHas('Customer.SpinWinners', function ($query) use ($spinner) {
                                                                                    $query->where("spinner_id", $spinner->id)
                                                                                        ->where("status", 1);
                                                                                })
                                                                                ->whereHas('Customer', function ($query) use ($spinner ) {
                                                                                    $query->where('created_at' , '>=' , $spinner->start_at)
                                                                                    ->where('created_at' , '<=' , $spinner->end_at)
                                                                                        ->where("status", 1);
                                                                                })
                                                                                ->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ])
                                                                                ->sum('purchases.amount');

            $this->response['data']["totalExistingCustomerSales"] =  App\Purchase::where("purchases.status", 1)
                                                                                ->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ])
                                                                                ->whereHas('Customer.SpinWinners', function ($query) use ($spinner) {
                                                                                    $query->where("spinner_id", $spinner->id)
                                                                                        ->where("status", 1);
                                                                                })
                                                                                ->whereHas('Customer', function ($query) use ($spinner ) {
                                                                                    $query->where("created_at" , '<=' , $spinner->start_at)
                                                                                        ->where("status", 1);
                                                                                })
                                                                                ->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ])
                                                                                ->sum('purchases.amount');

            $this->response['data']["totalCustomers"] =  App\Customer::where('status', 1)
                                                                        ->whereHas('SpinWinners', function ($query) use ($spinner) {
                                                                            $query->where("spinner_id", $spinner->id)
                                                                                  ->where("status", 1);
                                                                        })
                                                                        ->whereBetween("created_at" ,  [ $start_date , $end_date ])
                                                                        // ->where(function($query) use($spinner){
                                                                        //     $query->where('created_at' , '>=' , $spinner->start_at)
                                                                        //           ->where('created_at' , '<=' , $spinner->end_at);
                                                                        // })
                                                                        ->count();

            $this->response['data']["totalNewCustomers"] =  App\Customer::where('status', true)
                                                                         ->where(function($query) use ($spinner){
                                                                             $query->whereHas('SpinWinners', function ($query) use ($spinner) {
                                                                                $query->where("spinner_id", $spinner->id)
                                                                                      ->where("status", 1);
                                                                            });
                                                                         })
                                                                         ->where(function($query) use($spinner){
                                                                            $query->where('created_at' , '>=' , $spinner->start_at)
                                                                                  ->where('created_at' , '<=' , $spinner->end_at);
                                                                        })
                                                                         ->count();

            $this->response['data']["totalPurchases"] = App\Purchase::where("purchases.status", 1)
                                                                        ->whereBetween("purchases.created_at" ,  [ $start_date , $end_date ])
                                                                        ->whereHas('Customer.SpinWinners', function ($query) use ($spinner) {
                                                                            $query->where("spinner_id", $spinner->id)
                                                                                ->where("status", 1);
                                                                        })
                                                                        ->count();


            $this->response['data']["totalExistingCustomers"] = App\Customer::where('status', true)
                                                                            ->whereHas('SpinWinners', function ($query) use ($spinner) {
                                                                                $query->where("spinner_id", $spinner->id)
                                                                                    ->where("status", 1);
                                                                            })
                                                                            ->whereDate("created_at" , '<' , $spinner->start_at)
                                                                            ->count();


            $this->response['data']["totalGifts"] = App\SpinWinner::where('status', true)
                                                                    //->whereHas('GiftCode')
                                                                    ->whereNotNull('code')
                                                                    //->where('is_gifted' , 1)
                                                                    ->where("spinner_id" ,  $spinner->id)
                                                                    ->whereBetween("created_at" ,  [ $start_date , $end_date ])
                                                                    ->count();

            return $this->successResponse($this->response);
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }


    public function pdf(Request $request )
    {
        try {
            $spinner = $request->has('spinner_id') ? App\Spinner::find($request->spinner_id) : App\Spinner::first();

            if(!isset($spinner)){
                return $this->errorResponse($this->response);
            }
            $day_diff = 0;

            $filter = $request->all();

            
            //return [$start_date , $end_date];
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

                //$day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));
            } else{
                $start_date = $spinner->start_at;
                $end_date = $spinner->end_at;
            }

            $day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));

            //return [$start_date , $end_date];

            //return [$start_date , $end_date];
            $purchases = DB::table('purchases')->join('customers' , 'purchases.customer_id' , '='  , 'customers.id')
                                               ->whereExists(function ($query) use($start_date , $end_date) {
                                                    $query->select("spin_winners.id")
                                                        ->from('spin_winners')
                                                        ->whereRaw('customers.id = spin_winners.customer_id')
                                                        ->whereBetween('spin_winners.created_at' , [$start_date , $end_date]);
                                                })
                                                ->whereBetween('purchases.created_at' , [$start_date , $end_date]);

            if($request->type == 'new'){
                $purchases = $purchases->where('customers.created_at' , '>=' , $spinner->start_at);
            }else if($request->type == 'old'){
                $purchases = $purchases->where('customers.created_at' , '<=' , $spinner->start_at);
            }



            if($day_diff <= 30){
                $purchases = $purchases->selectRaw("DATE_FORMAT(purchases.created_at, '%Y-%m-%d') as date , sum(purchases.amount) as amount,  COUNT(DISTINCT customers.id) as total")
                                       ->groupBy(DB::Raw('date'));
            } else if($day_diff > 30 && $day_diff <= 365){
                $purchases = $purchases->selectRaw("DATE_FORMAT(purchases.created_at, '%Y-%m') as date , sum(purchases.amount) as amount,  COUNT(DISTINCT customer_id) as total")
                                       ->groupBy(DB::Raw('date'));
            } else if($day_diff > 365){
                $purchases = $purchases->selectRaw("DATE_FORMAT(purchases.created_at, '%Y') as date , sum(purchases.amount) as amount,   COUNT(DISTINCT customer_id) as total")
                                       ->groupBy(DB::Raw('date'));
            }

            
            $purchases = $purchases->get();

            //$headers = isset($purchases[0]) ? array_keys($purchases[0]) : [];
            $pdf = PDF::loadView('exports.spinner.purchases' , [ 'purchases' => $purchases ]);
            return $pdf->download('spinner_customers.pdf');
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $e;//$this->errorResponse($this->response);
        }
    }

    public function exports(Request $request )
    {
        try {
            $spinner = $request->has('spinner_id') ? App\Spinner::find($request->spinner_id) : App\Spinner::first();

            if(!isset($spinner)){
                return $this->errorResponse($this->response);
            }
            $day_diff = 0;

            $filter = $request->all();

            
            //return [$start_date , $end_date];
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

                //$day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));
            } else{
                $start_date = $spinner->start_at;
                $end_date = $spinner->end_at;
            }

            $day_diff = Carbon::parse($end_date)->diffInDays(Carbon::parse($start_date));

            //return [$start_date , $end_date];

            //return [$start_date , $end_date];
            $purchases = DB::table('purchases')->join('customers' , 'purchases.customer_id' , '='  , 'customers.id')
                                               ->whereExists(function ($query) use($start_date , $end_date) {
                                                    $query->select("spin_winners.id")
                                                        ->from('spin_winners')
                                                        ->whereRaw('customers.id = spin_winners.customer_id')
                                                        ->whereBetween('spin_winners.created_at' , [$start_date , $end_date]);
                                                })
                                                ->whereBetween('purchases.created_at' , [$start_date , $end_date]);

            if($request->type == 'new'){
                $purchases = $purchases->where('customers.created_at' , '>=' , $spinner->start_at);
            }else if($request->type == 'old'){
                $purchases = $purchases->where('customers.created_at' , '<=' , $spinner->start_at);
            }



            if($day_diff <= 30){
                $purchases = $purchases->selectRaw("DATE_FORMAT(purchases.created_at, '%Y-%m-%d') as date , sum(purchases.amount) as amount,  COUNT(DISTINCT customers.id) as total")
                                       ->groupBy(DB::Raw('date'));
            } else if($day_diff > 30 && $day_diff <= 365){
                $purchases = $purchases->selectRaw("DATE_FORMAT(purchases.created_at, '%Y-%m') as date , sum(purchases.amount) as amount,  COUNT(DISTINCT customer_id) as total")
                                       ->groupBy(DB::Raw('date'));
            } else if($day_diff > 365){
                $purchases = $purchases->selectRaw("DATE_FORMAT(purchases.created_at, '%Y') as date , sum(purchases.amount) as amount,   COUNT(DISTINCT customer_id) as total")
                                       ->groupBy(DB::Raw('date'));
            }

            
            $purchases = $purchases->get()->toArray();

            //$headers = isset($purchases[0]) ? array_keys($purchases[0]) : [];
            return Excel::download(new SpinnerCustomerReportExport($purchases), 'spin-and-win-customer-report.xlsx'); 
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $e;//$this->errorResponse($this->response);
        }
    }

    
}