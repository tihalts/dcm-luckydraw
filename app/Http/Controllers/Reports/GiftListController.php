<?php 

namespace App\Http\Controllers\Reports;

use PDF;
use App;
use Lang;
use Excel;
use App\Campaign;
use Carbon\Carbon;
use App\ScratchCard;
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

class GiftListController extends Controller 
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

            
            $campaigns = Campaign::where('status' , true)
                                   ->where("campaign_type" , "scratch_card")
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

                $campaign->gifts = App\GiftItem::whereHas("gift" , function($query) use($campaign){
                                                   $query->where("gift_id" , $campaign->id)
                                                         ->where("status" , true);
                                                 })
                                                 ->where(function($query) use($start_date , $end_date) {
                                                    if(isset($start_date) && isset($end_date)){
                                                        $query->whereBetween("gift_at" ,  [ $start_date , $end_date ]);
                                                    }                                                
                                                 }) 
                                                 ->where("status" , true)
                                                 ->count();

                $campaign->ungifts = App\GiftItem::whereHas("gift" , function($query) use($campaign){
                                                    $query->where("gift_id" , $campaign->id)
                                                          ->where("status" , true);
                                                  })
                                                  ->where(function($query) use($start_date , $end_date) {
                                                    if(isset($start_date) && isset($end_date)){
                                                        $query->whereBetween("gift_at" ,  [ $start_date , $end_date ]);
                                                    }                                                
                                                  }) 
                                                  ->where("status" , true)
                                                  ->whereNull("gifted_at")
                                                  ->count();

                $campaign->gifted = App\GiftItem::whereHas("gift" , function($query) use($campaign){
                                                    $query->where("gift_id" , $campaign->id)
                                                          ->where("status" , true);
                                                  })
                                                  ->where(function($query) use($start_date , $end_date) {
                                                    if(isset($start_date) && isset($end_date)){
                                                        $query->whereBetween("gifted_at" ,  [ $start_date , $end_date ]);
                                                    }                                                
                                                 }) 
                                                  ->where("status" , true)
                                                  ->whereNotNull("gifted_at")
                                                  ->count();
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

        $campaigns = Campaign::where('status' , true)
                               ->where("campaign_type" , "scratch_card")
                               ->where(function($query) use($start_date , $end_date) {
                                    if(isset($start_date) && isset($end_date)){
                                        $query->whereBetween("start_at" ,  [ $start_date , $end_date ]);
                                    }                                                
                                }); 

        $campaigns = $campaigns->orderBy('created_at' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                ->get(); 

        foreach ($campaigns as $key => $campaign) {

            $campaign->gifts = App\GiftItem::whereHas("gift" , function($query) use($campaign){
                                                $query->where("gift_id" , $campaign->id)
                                                        ->where("status" , true);
                                                })
                                                ->where("status" , true)
                                                ->count();

            $campaign->ungifts = App\GiftItem::whereHas("gift" , function($query) use($campaign){
                                                $query->where("gift_id" , $campaign->id)
                                                        ->where("status" , true);
                                                })
                                                ->where("status" , true)
                                                ->whereNull("gifted_at")
                                                ->count();

            $campaign->gifted = App\GiftItem::whereHas("gift" , function($query) use($campaign){
                                                $query->where("gift_id" , $campaign->id)
                                                        ->where("status" , true);
                                                })
                                                ->where("status" , true)
                                                ->whereNotNull("gifted_at")
                                                ->count();
        }

        $data = $campaigns;
        
        if($type == 'pdf'){         
            $pdf = PDF::loadView('exports.gifts.campaigns',  [ "data" => $data]);
            return $pdf->download('campaign_gifts.pdf');
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

            $cards = ScratchCard::where('status' , true)
                                ->select([
                                    DB::raw('DATE(created_at) as date'),
                                    DB::raw("count(id) as total_cards"),
                                    DB::raw("count(IF(is_winner = 1 , 1, null)) as total_winners")
                                ])
                                ->where(function($query) use($start_date , $end_date) {
                                    if(isset($start_date) && isset($end_date)){
                                        $query->whereBetween("created_at" ,  [ $start_date , $end_date ]);
                                    }                                                
                                })
                                ->groupBy(DB::raw('Date(created_at)'));

            $this->response["totalItems"] = $cards->count();

            $cards = $cards->orderBy('date' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                    ->offset(15 * ($page - 1))
                                    ->limit(15)
                                    ->get(); 

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response['data'] = $cards;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function export_days_pdf(Request $request, $type)
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

        $cards = ScratchCard::where('status' , true)
                            ->select([
                                DB::raw('DATE(created_at) as date'),
                                DB::raw("count(id) as total_cards"),
                                DB::raw("count(IF(is_winner = 1 , 1, null)) as total_winners")
                            ])
                            ->where(function($query) use($start_date , $end_date) {
                                if(isset($start_date) && isset($end_date)){
                                    $query->whereBetween("created_at" ,  [ $start_date , $end_date ]);
                                }                                                
                            })
                            ->groupBy(DB::raw('Date(created_at)'))
                            ->orderBy('date' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                            ->get(); 

        $data = $cards;
       
        if($type == 'pdf'){
            $pdf = PDF::loadView('exports.gifts.days',  [ "data" => $data]);
            return $pdf->download('gifts.pdf');
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
                                      ->withCount(["ScratchCards as total_cards" => function (Builder $query) use( $start_date , $end_date ) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->where("created_at" , [ $start_date , $end_date ]);
                                            }
                                        }])
                                        ->withCount(["ScratchCards as total_gifts" => function (Builder $query) use( $start_date , $end_date ) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->where("created_at" , [ $start_date , $end_date ]);
                                            }
                                            $query->where("is_winner" , true);
                                        }]);
                           

            $this->response["totalItems"] = $promoters->count();

            $promoters = $promoters->orderBy('total_cards' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
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
                                ->withCount(["ScratchCards as total_cards" => function (Builder $query) use( $start_date , $end_date ) {
                                    if(isset($start_date) && isset($end_date)){
                                        $query->where("created_at" , [ $start_date , $end_date ]);
                                    }
                                }])
                                ->withCount(["ScratchCards as total_gifts" => function (Builder $query) use( $start_date , $end_date ) {
                                    if(isset($start_date) && isset($end_date)){
                                        $query->where("created_at" , [ $start_date , $end_date ]);
                                    }
                                    $query->where("is_winner" , true);
                                }])
                                ->orderBy('total_cards' , isset($filter["orderby"]) ? $filter["orderby"] : "desc")
                                ->get(); 

        $data = $promoters;
       
        if($type == 'pdf'){           
            $pdf = PDF::loadView('exports.gifts.promoters', [ "data" => $data]);
            return $pdf->download('promoter_gifts.pdf');
        }else {
            return Excel::download(new ReportExport($data->toArray()), Uuid::uuid1(). '.xlsx');
        }
    }
}