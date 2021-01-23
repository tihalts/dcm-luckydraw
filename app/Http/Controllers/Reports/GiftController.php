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
use App\Exports\GiftsExport;
use App\Exports\ReportExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class GiftController extends Controller 
{

    var $response = [];
    var $filter = [
        "searchText" => "",
        "orderby" => "desc",
        "itemPerPage" => 10
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
            $giftitems = App\GiftItem::where('status' , true);
            

            if(isset($filter['gift_id'])){
                $giftitems = $giftitems->where('gift_id' , $filter['gift_id']);
            }

            if(isset($filter['campaign_id'])){
                $giftitems = $giftitems->whereHas('gift' , function($query) use($filter){
                    $query->where('campaign_id' , $filter['campaign_id']);
                });
            }

            if(isset($filter['user_id'])){
                $giftitems = $giftitems->whereHas('card' , function($query) use($filter){
                    $query->where('user_id' , $filter['user_id']);
                });
            }


            if(isset($filter['customer_id'])){
                $giftitems = $giftitems->whereHas('card' , function($query) use($filter){
                    $query->where('customer_id' , $filter['customer_id']);
                });
            }

            if(isset($filter['searchText'])){
                $giftitems = $giftitems->where(function($query) use($filter){
                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }  
            
            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $giftitems = $giftitems->whereBetween('gifted_at' ,[$start_date , $end_date]);
            }

            $this->response["totalItems"] = $giftitems->count();
            $this->response['data'] = $giftitems->with(['gift:id,name,campaign_id' , 'gift.campaign:id,name' , 'card', 'card.customer' , 'card.provider'])->orderBy('id' , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"])
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
            $page = isset($filter['page']) ? $filter['page'] : 1;
            $giftitems = App\GiftItem::where('status' , true);
            

            if(isset($filter['gift_id'])){
                $giftitems = $giftitems->where('gift_id' , $filter['gift_id']);
            }

            if(isset($filter['campaign_id'])){
                $giftitems = $giftitems->whereHas('gift' , function($query) use($filter){
                    $query->where('campaign_id' , $filter['campaign_id']);
                });
            }

            if(isset($filter['user_id'])){
                $giftitems = $giftitems->whereHas('card' , function($query) use($filter){
                    $query->where('user_id' , $filter['user_id']);
                });
            }


            if(isset($filter['customer_id'])){
                $giftitems = $giftitems->whereHas('card' , function($query) use($filter){
                    $query->where('customer_id' , $filter['customer_id']);
                });
            }

            if(isset($filter['searchText'])){
                $giftitems = $giftitems->where(function($query) use($filter){
                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }  
            
            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $giftitems = $giftitems->whereBetween('gifted_at' ,[$start_date , $end_date]);
            }

            $giftitems = $giftitems->with(['gift:id,name,campaign_id' , 'gift.campaign:id,name' , 'card', 'card.customer' , 'card.provider'])
                                ->get()
                                ->toArray(); 

            foreach($giftitems as $key => $gift){
                $export[$key]['Campaign Name'] = $gift['gift']['campaign']['name'];
                $export[$key]['Gift Name'] = $gift['gift']['name'];
                $export[$key]['Gift Code'] = isset($gift['card']['code']) ? $gift['card']['code'] : null;
                $export[$key]['Customer Name'] = isset($gift['card']) ? $gift['card']['customer']['fullname'] : null;
                $export[$key]['Customer CPR'] = isset($gift['card']) ? $gift['card']['customer']['cpr'] : null;
                $export[$key]['Customer Email'] = isset($gift['card']) ? $gift['card']['customer']['email'] : null;
                $export[$key]['Customer mobile'] = isset($gift['card']) ? $gift['card']['customer']['mobile'] : null;
                $export[$key]['Provider By'] = isset($gift['card']) ? $gift['card']['provider']['fullname'] : null;
                $export[$key]['Provider Role'] = isset($gift['card']) ? $gift['card']['provider']['role'] : null;
                $export[$key]['Scratched At'] = isset($gift['card']) ? $gift['card']['scratched_at'] : null;
                $export[$key]['Scratch Created At'] = isset($gift['card']) ? $gift['card']['created_at'] : null;

            }

            return Excel::download(new GiftsExport($export), 'gifts.xlsx');
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function export_pdf(Request $request, $type)
    {
        try {      
            
            $export = [];     
            $filter = $request->all(); 
            $page = isset($filter['page']) ? $filter['page'] : 1;
            $giftitems = App\GiftItem::where('status' , true);
            

            if(isset($filter['gift_id'])){
                $giftitems = $giftitems->where('gift_id' , $filter['gift_id']);
            }

            if(isset($filter['campaign_id'])){
                $giftitems = $giftitems->whereHas('gift' , function($query) use($filter){
                    $query->where('campaign_id' , $filter['campaign_id']);
                });
            }

            if(isset($filter['user_id'])){
                $giftitems = $giftitems->whereHas('card' , function($query) use($filter){
                    $query->where('user_id' , $filter['user_id']);
                });
            }


            if(isset($filter['customer_id'])){
                $giftitems = $giftitems->whereHas('card' , function($query) use($filter){
                    $query->where('customer_id' , $filter['customer_id']);
                });
            }

            if(isset($filter['searchText'])){
                $giftitems = $giftitems->where(function($query) use($filter){
                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }  
            
            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $giftitems = $giftitems->whereBetween('gifted_at' ,[$start_date , $end_date]);
            }

            if(isset($filter['start_date']) && !isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $giftitems = $giftitems->whereDate('gifted_at' ,$start_date );
            }

            $giftitems = $giftitems->whereHas('card', function($q){ $q->whereNotNull('code');})->with(['gift:id,name,campaign_id' , 'gift.campaign:id,name' , 'card', 'card.customer' , 'card.provider'])
                                ->get()
                                ->toArray(); 

            foreach($giftitems as $key => $gift){
                $export[$key]['Campaign Name'] = $gift['gift']['campaign']['name'];
                $export[$key]['Gift Name'] = $gift['gift']['name'];
                $export[$key]['Gift Code'] = isset($gift['card']['code']) ? $gift['card']['code'] : null;
                $export[$key]['Customer Name'] = isset($gift['card']) ? $gift['card']['customer']['fullname'] : null;
                $export[$key]['Customer CPR'] = isset($gift['card']) ? $gift['card']['customer']['cpr'] : null;
                $export[$key]['Customer Email'] = isset($gift['card']) ? $gift['card']['customer']['email'] : null;
                $export[$key]['Customer mobile'] = isset($gift['card']) ? $gift['card']['customer']['mobile'] : null;
                $export[$key]['Provider By'] = isset($gift['card']) ? $gift['card']['provider']['fullname'] : null;
                $export[$key]['Provider Role'] = isset($gift['card']) ? $gift['card']['provider']['role'] : null;
                $export[$key]['Scratched At'] = isset($gift['card']) ? $gift['card']['scratched_at'] : null;
                $export[$key]['Scratch Created At'] = isset($gift['card']) ? $gift['card']['created_at'] : null;

            }

            if($type == 'pdf'){
                $pdf = PDF::loadView('exports.report', ["reports" => $export]);
                //$pdf->setPaper('A4', 'landscape');
                return $pdf->download('gift_report.pdf');
            }else{
                return Excel::download(new ReportExport($export), 'gift_report.xlsx');
            }

            // $pdf = PDF::loadView('exports.gift' , ['gifts' => $export ]);
            // return $pdf->download('gifts.pdf');
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function search(Request $request)
    {
        try {      
            $filter = $request->all(); 
            $campaigns = App\Gift::where('status' , true);

            if(isset($filter['campaign_id'])){
                $campaigns = App\Gift::where('campaign_id' , $request->campaign_id);
            }

            if(isset($filter['searchText'])){
                $campaigns = $campaigns->where(function($query) use($filter){
                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
                            });
            }    

            $this->response['data'] = $campaigns->orderBy('id' , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"])
                                                ->get();
                                            
            
           
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function ItemReport(Request $request)
    {
        try { 
            
            $this->response['data']["totalGiftItems"] = $this->item_report($request);

            $this->response['data']["totalUnGiftItems"] = $this->item_report($request , 'ungifted');

            $this->response['data']["totalGiftedItems"] = $this->item_report($request , 'gifted');

            $this->response['data']["todayGiftedItems"] = $this->item_report($request , 'today');
           
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function item_report(Request $request , $type = 'total')
    {
        $filter = $request->all(); 
        $page = isset($filter['page']) ? $filter['page'] : 1;
        $giftitems = App\GiftItem::where('status' , true);
        

        if(isset($filter['gift_id'])){
            $giftitems = $giftitems->where('gift_id' , $filter['gift_id']);
        }

        if(isset($filter['campaign_id'])){
            $giftitems = $giftitems->whereHas('gift' , function($query) use($filter){
                $query->where('campaign_id' , $filter['campaign_id']);
            });
        }

        if(isset($filter['user_id'])){
            $giftitems = $giftitems->whereHas('card' , function($query) use($filter){
                $query->where('user_id' , $filter['user_id']);
            });
        }


        if(isset($filter['customer_id'])){
            $giftitems = $giftitems->whereHas('card' , function($query) use($filter){
                $query->where('customer_id' , $filter['customer_id']);
            });
        }


        if(isset($filter['start_date']) && isset($filter['end_date'])){
            $start_date = Carbon::parse($filter['start_date'])->startOfDay();
            $end_date = Carbon::parse($filter['end_date'])->endOfDay();
            $giftitems = $giftitems->whereBetween('gift_at' ,[$start_date , $end_date]);
        }else{
            $giftitems = $giftitems->whereBetween('gift_at' ,[ now()->subDays(30) , now() ]);
        }

        if($type == 'ungifted'){
            $giftitems = $giftitems->whereNull('gifted_at');
        }
        
        if($type == 'gifted'){
            $giftitems = $giftitems->whereNotNull('gifted_at');
        }
        
        if($type == 'today'){
            $giftitems = $giftitems->whereDate('gift_at' , Carbon::now());
        }

        return $giftitems->count();
       
    }

    public function campaigns(Request $request)
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

            

            $campaigns = App\Campaign::where('status' , true)
                                        ->where(function($query) use($start_date , $end_date) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->whereBetween("start_at" ,  [ $start_date , $end_date ]);
                                            }
                                        })
                                        ->orderBy('created_at' , 'desc')
                                        ->take(10)
                                        ->get(); 

            $labels = [];

            foreach ($campaigns as $key => $campaign) {

                $labels[] = $campaign->name;

                $total_items = App\GiftItem::where('status' , true)
                                            ->whereHas("gift" , function($query) use($campaign){
                                                $query->where("campaign_id" , $campaign->id);
                                            })
                                            ->where(function($query) use($start_date , $end_date) {
                                                if(isset($start_date) && isset($end_date)){
                                                    $query->whereBetween("created_at" ,  [ $start_date , $end_date ]);
                                                }                                                
                                            })
                                            ->count();

                $ungift_items = App\GiftItem::where('status' , true)
                                             ->whereNull("gifted_at")
                                            ->whereHas("gift" , function($query) use($campaign){
                                                $query->where("campaign_id" , $campaign->id);
                                            })
                                            ->where(function($query) use($start_date , $end_date) {
                                                if(isset($start_date) && isset($end_date)){
                                                    $query->whereBetween("created_at" ,  [ $start_date , $end_date ]);
                                                }
                                            })
                                            ->count();
                                                        
                $toArray[] = [  'x' => $campaign->name , 'y' => (int) $total_items , 'z' => (int) $ungift_items];
            }

            $this->response['day_diff'] = $day_diff;
            $this->response['labels'] = $labels;
            $this->response['data'] = $toArray;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function promoters(Request $request)
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

            $campaigns = App\Campaign::where('status' , true)
                                        ->where('campaign_type' , 'scratch_card')
                                        ->where(function($query) use($start_date , $end_date) {
                                            if(isset($start_date) && isset($end_date)){
                                                $query->whereBetween("start_at" ,  [ $start_date , $end_date ]);
                                            }                                                
                                        })
                                        ->orderBy('created_at' , 'desc')                                        
                                        ->take(10)
                                        ->get(); 

            $labels = [];
            foreach ($campaigns as $key => $campaign) {

                $labels[] = $campaign->name;

                $total_items = App\ScratchCard::where('status' , true)
                                            //->whereBetween('created_at' , [ $campaign->start_at , $campaign->end_at ])
                                            ->where(function($query) use($start_date , $end_date) {
                                                if(isset($start_date) && isset($end_date)){
                                                    $query->whereBetween("created_at" ,  [ $start_date , $end_date ]);
                                                }                                                
                                            })
                                            ->whereHas("gift")
                                            ->count();
                                                        
                $toArray[] = [  'x' => $campaign->name , 'y' => (int) $total_items ];
            }

            $this->response['day_diff'] = $day_diff;
            $this->response['labels'] = $labels;
            $this->response['data'] = $toArray;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function days(Request $request)
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
            
            $gifts = App\ScratchCard::where('status' , true)
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
                                        ->take(10)
                                        ->get(); 

            $labels = [];

            foreach ($gifts as $key => $gift) {

                $labels[] = $gift->date;
              
                                                        
                $toArray[] = [  'x' => $gift->date , 'y' => (int) $gift->total_winners , 'z' => (int) $gift->total_cards];
            }

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