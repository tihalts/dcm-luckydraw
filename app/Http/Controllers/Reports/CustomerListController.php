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

class CustomerListController extends Controller 
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

            $start_date = null;
            $end_date = null;
            $day_diff = 0;

            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $day_diff = $end_date->diffInDays($start_date);                
            } 

            
            $countries = App\Country::where('status' , true)
                                      ->whereHas('customers' , function (Builder $query) use( $start_date , $end_date ) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->where("created_at" , [ $start_date , $end_date ]);
                                            }
                                      })
                                      ->withCount(["customers as total_users" => function (Builder $query) use( $start_date , $end_date ) {
                                        if(isset($start_date) && isset($end_date)){
                                            $query->where("created_at" , [ $start_date , $end_date ]);
                                        }
                                  }]);
                           

            $this->response["totalItems"] = $countries->count();

            $countries = $countries->orderBy('total_users' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
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

    public function export_country_pdf(Request $request , $type)
    {
        $filter = $request->all();

        $start_date = isset($filter['start_date']) ? Carbon::parse($filter['start_date'])->startOfDay() : null;
        $end_date = isset($filter['end_date']) ?  Carbon::parse($filter['end_date'])->endOfDay() : Carbon::now()->endOfDay();

        if(!isset($start_date)){
            $start_date = Carbon::parse($end_date)->subDays(30)->startOfDay();
        }

        $countries = App\Country::where('status' , true)
                                ->whereHas('customers' , function (Builder $query) use( $start_date , $end_date ) {
                                    if(isset($start_date) && isset($end_date)){
                                        $query->where("created_at" , [ $start_date , $end_date ]);
                                    }
                                })
                                ->withCount(["customers as total_users" => function (Builder $query) use( $start_date , $end_date ) {
                                    if(isset($start_date) && isset($end_date)){
                                        $query->where("created_at" , [ $start_date , $end_date ]);
                                    }
                                }])
                                ->orderBy('total_users' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                ->get(); 

        $data = $countries;
        // Send data to the view using loadView function of PDF facade
        if($type == 'pdf'){
            $pdf = PDF::loadView('exports.customers.countries', ["data" => $data]);
            // If you want to store the generated pdf to the server then you can use the store function
            //$pdf->save(storage_path().'_filename.pdf');
            // Finally, you can download the file using download function
            return $pdf->download('country_customers.pdf');
        }else {
            return Excel::download(new ReportExport($data->toArray()), Uuid::uuid1(). '.xlsx');
        }
       
    }

    public function Vouchers(Request $request)
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

            $customers = App\Customer::where('status' , true)
                                      ->whereHas('vouchers' , function (Builder $query) use( $start_date , $end_date ) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->where("created_at" , [ $start_date , $end_date ]);
                                            }
                                      })
                                      ->withCount(["vouchers as total_vouchers" => function (Builder $query) use( $start_date , $end_date ) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->where("created_at" , [ $start_date , $end_date ]);
                                            }
                                    }]);

            if(isset($filter['searchText'])){
                $customers = $customers->where(function($query) use($filter){
                                $query->where('first_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                        ->orWhere('last_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                        ->orWhere('mobile' , 'LIKE' , '%'.$filter['searchText'].'%')
                                        ->orWhere('cpr' , 'LIKE' , '%'.$filter['searchText'].'%')
                                        ->orWhere('email' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }

            $this->response["totalItems"] = $customers->count();

            $customers = $customers->orderBy('total_vouchers' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                    ->offset(15 * ($page - 1))
                                    ->limit(15)
                                    ->get(); 

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $customers;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function export_voucher_pdf(Request $request , $type)
    {
        $filter = $request->all();
        
        $start_date = isset($filter['start_date']) ? Carbon::parse($filter['start_date'])->startOfDay() : null;
        $end_date = isset($filter['end_date']) ?  Carbon::parse($filter['end_date'])->endOfDay() : Carbon::now()->endOfDay();

        if(!isset($start_date)){
            $start_date = Carbon::parse($end_date)->subDays(30)->startOfDay();
        }

        if(isset($filter['start_date']) && isset($filter['end_date'])){
            $start_date = Carbon::parse($filter['start_date'])->startOfDay();
            $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            $day_diff = $end_date->diffInDays($start_date);
            
        } 

        $customers = App\Customer::where('status' , true)
                                      ->whereHas('vouchers' , function (Builder $query) use( $start_date , $end_date ) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->where("created_at" , [ $start_date , $end_date ]);
                                            }
                                      })
                                      ->withCount(["vouchers as total_vouchers" => function (Builder $query) use( $start_date , $end_date ) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->where("created_at" , [ $start_date , $end_date ]);
                                            }
                                    }])
                                    ->orderBy('total_vouchers' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                    ->get(); 

        $data = $customers;
       
        if($type == 'pdf'){           
            $pdf = PDF::loadView('exports.customers.vouchers', ["data" => $data]);
            return $pdf->download('customer_vouchers.pdf');
        }else {
            return Excel::download(new ReportExport($data->toArray()), Uuid::uuid1(). '.xlsx');
        }        
    }
    
    public function Gifts(Request $request)
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

            $customers = App\Customer::where('status' , true)
                                      ->whereHas('ScratchCards' , function (Builder $query) use( $start_date , $end_date ) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->where("created_at" , [ $start_date , $end_date ]);
                                            }
                                      })
                                      ->withCount(["ScratchCards as total_cards" , 
                                       'ScratchCards as total_gifts' => function (Builder $query) use( $start_date , $end_date ) {
                                        if(isset($start_date) && isset($end_date)){
                                            $query->where("created_at" , [ $start_date , $end_date ]);
                                        }
                                        $query->whereNotNull("gift_id");
                                     }]);

            $this->response["totalItems"] = $customers->count();

            $customers = $customers->orderBy('total_gifts' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                    ->offset(15 * ($page - 1))
                                    ->limit(15)
                                    ->get(); 

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $customers;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function export_gift_pdf(Request $request , $type)
    {
        $filter = $request->all();

        $start_date = isset($filter['start_date']) ? Carbon::parse($filter['start_date'])->startOfDay() : null;
        $end_date = isset($filter['end_date']) ?  Carbon::parse($filter['end_date'])->endOfDay() : Carbon::now()->endOfDay();

        if(!isset($start_date)){
            $start_date = Carbon::parse($end_date)->subDays(30)->startOfDay();
        }

        $customers = App\Customer::where('status' , true)
                                      ->whereHas('ScratchCards' , function (Builder $query) use( $start_date , $end_date ) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->where("created_at" , [ $start_date , $end_date ]);
                                            }
                                      })
                                      ->withCount(["ScratchCards as total_cards" , 
                                       'ScratchCards as total_gifts' => function (Builder $query) use( $start_date , $end_date ) {
                                        if(isset($start_date) && isset($end_date)){
                                            $query->where("created_at" , [ $start_date , $end_date ]);
                                        }
                                        $query->whereNotNull("gift_id");
                                     }])
                                     ->orderBy('total_gifts' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                    ->get(); 

        $data = $customers;

        if($type == 'pdf'){           
            $pdf = PDF::loadView('exports.customers.gifts', ["data" => $data]);
            return $pdf->download('customer_gifts.pdf');

        }else {
            return Excel::download(new ReportExport($data->toArray()), Uuid::uuid1(). '.xlsx');
        }
    }
}