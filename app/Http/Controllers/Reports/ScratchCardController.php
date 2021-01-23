<?php 

namespace App\Http\Controllers\Reports;

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
use App\Exports\ScratchCardsExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Uuid;

class ScratchCardController extends Controller 
{

    var $response = [];
    var $filter = [
        "searchText" => "",
        "itemPerPage" => 15,
        "orderby" => "desc",
        "active" => "all",
    ];

    var $day_settings = [];

    public function __construct()
    {        
        
    }
    
    public function index()
    {

    }

    public function list(Request $request , $page = 1)
    {
        try {      
            $filter = $request->all(); 
            $page = isset($filter['page']) ? $filter['page'] : 1;
            $scratchcards = App\ScratchCard::where('status' , true);

            $start_date = null;
            $end_date = null;
            $day_diff = 0;

            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $day_diff = $end_date->diffInDays($start_date);                
            } 

            if(isset($filter['campaign_id'])){
                $scratchcards = $scratchcards->where('campaign_id' , $filter['campaign_id']);
            }

            if(isset($filter['customer_id'])){
                $scratchcards = $scratchcards->where('customer_id' , $filter['customer_id']);
            }

            if(isset($filter['user_id'])){
                $scratchcards = $scratchcards->where('user_id' , $filter['user_id']);
            }

            $scratchcards = $scratchcards->where(function($query) use($start_date , $end_date) {
                                                if(isset($start_date) && isset($end_date)){
                                                    $query->whereBetween("scratched_at" ,  [ $start_date , $end_date ]);
                                                }                                                
                                            });

            $this->response["totalItems"] = $scratchcards->count();
            $this->response['data'] = $scratchcards->with(['Customer' ,'Gift' , 'Campaign' , 'Provider'])
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
            $scratchcards = App\ScratchCard::where('status' , true);

            $start_date = null;
            $end_date = null;
            $day_diff = 0;

            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $day_diff = $end_date->diffInDays($start_date);                
            } 

            if(isset($filter['campaign_id'])){
                $scratchcards = $scratchcards->where('campaign_id' , $filter['campaign_id']);
            }

            if(isset($filter['customer_id'])){
                $scratchcards = $scratchcards->where('customer_id' , $filter['customer_id']);
            }

            if(isset($filter['user_id'])){
                $scratchcards = $scratchcards->where('user_id' , $filter['user_id']);
            }
            $scratchcards = $scratchcards->with(['Customer' ,'Gift' , 'Campaign' , 'Provider'])
                                        ->where(function($query) use($start_date , $end_date) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->whereBetween("scratched_at" ,  [ $start_date , $end_date ]);
                                            }                                                
                                        }) 
                                        ->get(); 

            foreach($scratchcards as $key => $card){
                $export[$key]['Campaign Name'] = $card['campaign']['name'];
                $export[$key]['Customer Name'] = $card['customer']['fullname'];
                $export[$key]['Customer CPR'] = $card['customer']['cpr'];
                $export[$key]['Customer Email'] = $card['customer']['email'];
                $export[$key]['Customer mobile'] = $card['customer']['mobile'];
                $export[$key]['Gift Name'] = isset($card['gift']) ? $card['gift']['name'] : null;
                $export[$key]['Gift Code'] = $card['code'];
                $export[$key]['Provider By'] = isset($card['provider']['fullname']) ? $card['provider']['fullname'] : null;
                $export[$key]['Provider Role'] = isset($card['provider']['role']) ? $card['provider']['role'] : null;
                $export[$key]['Scratched At'] = $card['scratched_at'];
                $export[$key]['Created At'] = $card['created_at'];

            }

            return Excel::download(new ScratchCardsExport($export), 'scratch-cards.xlsx');
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function line_chart(Request $request)
    {
        $filter = $request->all(); 
        $page = isset($filter['page']) ? $filter['page'] : 1;
        $scratchcards = App\ScratchCard::where('status' , true);

        if(isset($filter['campaign_id'])){
            $scratchcards = $scratchcards->where('campaign_id' , $filter['campaign_id']);
        }

        if(isset($filter['customer_id'])){
            $scratchcards = $scratchcards->where('customer_id' , $filter['customer_id']);
        }

        if(isset($filter['user_id'])){
            $scratchcards = $scratchcards->where('user_id' , $filter['user_id']);
        }

        if(isset($filter['start_date']) && isset($filter['end_date']) ){
            $start_date = Carbon::parse($filter['start_date'])->startOfDay();
            $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            $scratchcards = $scratchcards->whereBetween('created_at' ,[$start_date , $end_date]);
        }else{
            $scratchcards = $scratchcards->whereBetween('created_at' ,[ now()->subDays(30) , now() ]);
        }

        $scratchcards = $scratchcards->select(array(DB::Raw('count(*) as count'),DB::Raw('DATE(created_at) day')))
                                    ->groupBy('day')
                                    ->orderBy('day')
                                    ->get()  
                                    ->pluck('count' , 'day'); 


        $this->response['data'] = $scratchcards;
        return $this->successResponse($this->response);
    }

    public function export_pdf(Request $request , $type)
    {
        try {      
            $export = [];
            $filter = $request->all(); 

            $start_date = isset($filter['start_date']) ? Carbon::parse($filter['start_date'])->startOfDay() : null;
            $end_date = isset($filter['end_date']) ?  Carbon::parse($filter['end_date'])->endOfDay() : Carbon::now()->endOfDay();

            if(!isset($start_date)){
                $start_date = Carbon::parse($end_date)->subDays(30)->startOfDay();
            }

            $scratchcards = App\ScratchCard::where('status' , true);

            if(isset($filter['campaign_id'])){
                $scratchcards = $scratchcards->where('campaign_id' , $filter['campaign_id']);
            }

            if(isset($filter['customer_id'])){
                $scratchcards = $scratchcards->where('customer_id' , $filter['customer_id']);
            }

            if(isset($filter['user_id'])){
                $scratchcards = $scratchcards->where('user_id' , $filter['user_id']);
            }
            $scratchcards = $scratchcards->with(['Customer' ,'Gift' , 'Campaign' , 'Provider'])
                                        ->whereBetween('created_at' ,[$start_date , $end_date])
                                        ->get(); 

            foreach($scratchcards as $key => $card){
                $export[$key]['Campaign Name'] = $card['campaign']['name'];
                $export[$key]['Customer Name'] = $card['customer']['fullname'];
                $export[$key]['Customer CPR'] = $card['customer']['cpr'];
                $export[$key]['Customer Email'] = $card['customer']['email'];
                $export[$key]['Customer mobile'] = $card['customer']['mobile'];
                $export[$key]['Gift Name'] = isset($card['gift']) ? $card['gift']['name'] : null;
                $export[$key]['Gift Code'] = $card['code'];
                $export[$key]['Provider By'] = isset($card['provider']['fullname']) ? $card['provider']['fullname'] : null;
                $export[$key]['Provider Role'] = isset($card['provider']['role']) ? $card['provider']['role'] : null;
                $export[$key]['Scratched At'] = $card['scratched_at'];
                $export[$key]['Created At'] = $card['created_at'];

            }
            
            if($type == 'pdf'){
                $pdf = PDF::loadView('exports.scratchcard' , ['cards' => $export ]);
                return $pdf->download('scratchcards.pdf');
            }else {
                return Excel::download(new ReportExport($export), Uuid::uuid1(). '.xlsx');
            }
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }
}