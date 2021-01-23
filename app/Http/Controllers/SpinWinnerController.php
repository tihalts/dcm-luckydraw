<?php 

namespace App\Http\Controllers;

use App;
use Lang;
use Config;
use Carbon\Carbon;
use Illuminate\Http\File;
use App\Events\SpunWinner;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;


class SpinWinnerController extends Controller 
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
            $spinners = App\SpinWinner::where('status' , true); 

            if($request->has('spinner_id')){
                $spinners = $spinners->where('spinner_id' , $request->has('spinner_id'));
            }

            if($request->has('customer_id')){
                $spinners = $spinners->where('customer_id' , $request->has('customer_id'));
            }

            $this->response["totalItems"] = $spinners->count();
            $this->response['data'] = $spinners->with(['Customer' , 'Gift' , 'Spinner' , 'Provider'])
                                                ->latest()
                                                ->offset($this->filter["itemPerPage"] * ($page - 1))
                                                ->limit($this->filter["itemPerPage"])
                                                ->get();
            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response['user'] = Auth::user();
            
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function search(Request $request , $page = 1)
    {
        try {      
            $filter = $request->all(); 
            $spinners = App\SpinWinner::where('status' , true);

            if($request->has('spinner_id')){
                $spinners = $spinners->where('spinner_id' , $request->has('spinner_id'));
            }

            if(isset($filter['customer_id'])){
                $spinners = $spinners->where('customer_id' , $filter['customer_id']);
            }

            if(isset($filter['promoter_id'])){
                $spinners = $spinners->where('user_id' , $filter['promoter_id']);
            }


            if(isset($filter['searchText'])){

                $spinners = $spinners->where('code' , 'LIKE' , '%'.$filter['searchText'].'%')
                                       ->orWhereHas('customer' , function($query) use($filter){
                                            $query->where(function($query) use($filter){
                                                $query->where('first_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('last_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('mobile' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('cpr' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('email' , 'LIKE' , '%'.$filter['searchText'].'%');
                                            });
                                       })->orWhereHas('Spinner' , function($query) use($filter){
                                            $query->where(function($query) use($filter){
                                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
                                            });
                                       });
            }    


            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $spinners = $spinners->whereDate('created_at' , '>=' , $start_date)
                                                 ->whereDate('created_at' , '<=' , $end_date);
            }
            
            $this->response["totalItems"] = $spinners->count();

            if(isset($filter['filter_by'])){
                $spinners = $spinners->orderBy($filter['filter_by'] , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"]);
            } else{
                $spinners = $spinners->orderBy('id' , 'desc');
            }

            $this->response['data'] = $spinners->with(['Customer' ,'Gift' , 'Spinner' , 'Provider'])
                                                ->offset($filter["itemPerPage"] ? $filter["itemPerPage"] * ($page - 1) : $this->filter["itemPerPage"] * ($page - 1))
                                                ->limit($filter["itemPerPage"] ? $filter["itemPerPage"] : $this->filter["itemPerPage"])
                                                ->get(); 

            $this->response["currentPage"] = $page;
            $this->response["filter"] = $filter;
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function unredeemed_spuns($customer_id)
    {
        $spinners = App\SpinWinner::whereHas('Spinner' , function($query){
                                            $query->whereDate('start_at' , '<=' , Carbon::now())
                                                  ->whereDate('end_at' , '>=' , Carbon::now())
                                                  ->where('status' , true);
                                        })
                                        ->whereHas('Spinner.Gifts.Items' , function($query){
                                            $query->whereNull('spin_gift_items.gifted_at')
                                                  ->whereDate('spin_gift_items.gift_at' , Carbon::now())
                                                  ->where('spin_gift_items.status' , true);
                                        })
                                        ->where('customer_id' , $customer_id)
                                        ->whereNull('spin_at')
                                        ->get();

        $spin_ids = [];
        
        if(isset($spinners))
        {
            foreach($spinners as $spin){
                $spin_ids[] = $spin->id;
            }
        }
         
        

        $this->response["data"] = isset($spin_ids) ? $this->spinnerInfos($spin_ids) : [];
            
        return $this->successResponse($this->response);
    }
    
    public function today_gifts($customer_id)
    {
        $gifts = App\SpinGiftItem::whereHas('Winner' , function($query) use($customer_id){
                                            $query->whereDate('spin_at' , '<=' , Carbon::now()) 
                                                  ->where('is_gifted' , 0)                    
                                                  ->where('customer_id' , $customer_id)
                                                  //->where('is_winner' , 1)
                                                  ->where('status' , true);
                                        })
                                        ->with("gift")
                                        ->get();

        $toArray = [];
        
        foreach($gifts as $key => $gift){
            $toArray[$key]['name'] = $gift->gift->name;
            $toArray[$key]['code'] = $gift->code;
            //$toArray[$key]['all'] = $gift;
        }

        $this->response["data"] = $toArray;

        $this->response['unspin'] = App\SpinWinner::where('status' , true)
                                                    ->whereNull('spin_at')
                                                    ->where('customer_id' , $customer_id)
                                                    ->count();

        return $this->successResponse($this->response);
    }

    public function spuninfos(Request $request , $customer_id)
    {
        if($request->has("spuns")){
            $spins = is_array($request->input("spuns")) ? $request->input("spuns") : [] ;
            $this->response["data"] = isset($spins) ? $this->spinnerInfos($spins) : [];
            return $this->successResponse($this->response);
        } 
        $this->response["data"] = [];
        return $this->successResponse($this->response);
    }

    public function unspuns($customer_id)
    {
        $spinner = App\Spinner::whereHas('Spins' , function($query) use($customer_id){
                                    $query->where('status' , true)
                                          ->whereNull('spin_at')
                                          ->where('customer_id' , $customer_id);
                                })
                                //->whereDate('start_at' , '<=' , Carbon::now())
                                //->whereDate('end_at' , '>=' , Carbon::now())
                                ->where('status' , true)
                                ->with(['items' , 'items.gift'])
                                ->withCount(['items as item_count'])
                                ->latest()
                                ->first();

        $toArray = [];
        $spin = null;

        if(isset($spinner)){
            $spin = self::ShuffledGifts($spinner , $customer_id);
        }
        

        if(isset($spin)){
            $toArray['id'] = $spinner->id;
            $toArray['name'] = $spinner->name;
            $toArray['spins'] = App\SpinWinner::where('status' , true)
                                                ->whereNull('spin_at')
                                                ->where('customer_id' , $customer_id)
                                                ->count();

            $toArray['item_count'] = $spinner->item_count;
            $toArray['spin_id'] = $spin->id;
            $toArray['gift_id'] = $spin->gift_id;
            $toArray['gift_segment'] = false;

            foreach ($spinner->items as $key => $item) {
                $toArray['segments'][$key] = [
                    "type" => "string",
                    "fillStyle" => $item->bg_color,
                    "value" =>  $item->name,
                    "win" => false,
                    "textFontSize" => 16,
                    "textFillStyle"=>  $item->text_color,
                    "resultText" => isset($item->gift) ? $item->gift->name : null,
                    "gift_id" => isset($item->gift) ? $item->gift->id : null,
                    "userData" => [ "gift_id" => isset($item->gift) ? $item->gift->id : null ],
                    //"probability" => isset($spin->gift_id) && isset($item->gift)  ? $spin->gift_id == $item->gift->id ? 100 : 0 : 0
                ];

                if(isset($spin->gift_id) && isset($item->gift)){

                    if($spin->gift_id == $item->gift->id ){
                        $toArray['segments'][$key]['probability'] = 100;
                    } else {
                        $toArray['segments'][$key]['probability'] = 0;
                    }
                    
                } else{
                    $toArray['segments'][$key]['probability'] = 0;
                }

                // if(isset($spin->gift_id)){
                //     $toArray['gift_segment'] = true;
                // }
                
            }

            if(!isset($spin->gift_id)){
                
                foreach ( $toArray['segments'] as $key => $value) {
                    if(!isset($value['gift_id'])) {
                        $toArray['segments'][$key]['probability'] = rand(95,100);
                        //break;
                    }
                }
            }
        }

        $this->response["data"] =  $toArray;
        return $this->successResponse($this->response);
    }

    public function ShuffledGifts($spinner , $customer_id)
    {
        $spin = App\SpinWinner::where('status' , true)
                                ->whereNull('spin_at')
                                ->where('customer_id' , $customer_id)
                                ->where('spinner_id' , $spinner->id)
                                ->first();

        $max_gifts = isset($spinner->data['customer_gift_limit']) ?  $spinner->data['customer_gift_limit'] : 0;

      

        if(isset($spin)){

            if($max_gifts != 0){
                $gifts = App\SpinWinner::where('status' , true)
                                        ->where('customer_id' , $customer_id)
                                        ->where('spinner_id' , $spinner->id)
                                        ->whereNotNull('gift_id')
                                        ->count();
    
                if($gifts >= $max_gifts){
                    $spin->update([ 'is_gifted' => false ]);
                    return $spin;
                }   
                
            }
            $probability = isset($spinner->data['probability']) ? $spinner->data['probability'] : 5;

            if($spin->is_gifted == false){
                $gift = $this->rand_gift($spinner->id , $probability);

                if(isset($gift)){
                    if(isset($gift->gift_id)){
                        // $gift = App\SpinGiftItem::where('gift_id' , $rand_item->gift_id)
                        //                             ->whereNull('gifted_at')
                        //                             ->whereDate('gift_at' , Carbon::now())
                        //                             ->where('status' , true)
                        //                             ->first();                         

                       
                        if(isset($gift)){
                            $old_gift = App\SpinWinner::where('status' , true)
                                                        ->where('customer_id' , $customer_id)
                                                        ->where('gift_id' , $gift->gift_id)
                                                        ->count();

                            if($old_gift == 0){
                                $spin->update(['gift_id' => $gift->gift_id , 'code' => $gift->code , 'is_gifted' => false]);
                            } else{
                                $spin->update([ 'is_gifted' => false ]);
                            }                            
                        }
                    }
                }
            }
        }

        return $spin;
    }

    public function rand_gift($spinner_id , $probability = 5)
    {
        $gift_codes = App\SpinGiftItem::whereHas('Gift' , function($query) use($spinner_id){
                                        $query->where('spinner_id' , $spinner_id);
                                    })
                                    ->whereNull('gifted_at')
                                    ->whereDate('gift_at' , Carbon::now())
                                    ->where('status' , true)
                                    ->orderByRaw('RAND()')
                                    ->first();

        if(!isset($gift_codes->code)){
            return null;
        }
        
        $generated_codes = $this->generated_codes($probability);
        $generated_codes[] = $gift_codes->code;
        $codes = $generated_codes;        
        $codes = collect($codes)->shuffle();        
        $code = $codes->random();

        $item =  App\SpinGiftItem::whereHas('Gift' , function($query) use($spinner_id){
                                    $query->where('spinner_id' , $spinner_id);
                                })
                                ->whereNull('gifted_at')
                                ->where('code' , $code)
                                ->first();

        if(isset($item)){
            $item->update(['gifted_at' => Carbon::now()]);
        }
        
        return isset($item) ? $item : null;
    }

    public function generated_codes($probability = 5)
    {
        
        $codes = [];
        for ($i=0; $i < $probability ; $i++) { 
            $codes[] = str_random(32);
        }
        return $codes;
    }

    public function spinnerInfos($spin_ids)
    {
        $spins = [];
        $spinners = App\SpinWinner::with(['items' , 'items.gift'])->whereIn('id' , $spin_ids)->get();
        foreach ($spinners as $spin) {
            $gift = $spin->gift;
            $spinner = $spin->spinner;

            //{'fillStyle' : '#000000', 'text' : 'BANKRUPT', 'textFontSize' : 16, 'textFillStyle' : '#ffffff'},
            $spins[] = [
                'id' => $spin->id,
                'spinner_name' => $spinner->name,
                'spinner_id' => $spin->spinner_id,
                'customer_id' => $spin->customer_id,
                'is_winner' => $spin->is_winner,
                'spin_at' => isset($spin->spin_at) ? true : false,
            ];
        }
        return $spins;
    }

    public function getItems($spinner_id = null)
    {
        $this->day_settings = config('spinner_settings');
        $gift_items = App\SpinGiftItem::whereHas('Gift' , function($query) use($spinner_id){
                                        $query->where('spinner_id' , $spinner_id);
                                    })
                                    ->whereNull('gifted_at')
                                    ->whereDate('gift_at' , Carbon::now())
                                    ->where('status' , true);
        $percentage = 0;
        $day = Carbon::now()->dayOfWeek;
        foreach ($this->day_settings[$day] as $times) {
            if($times){
                break;
            }
        }
    }

    public function fetch($id)
    {
        try{
            $Spin = App\SpinWinner::find($id);
            $this->response["data"] = $Spin;
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function SpunCompleted(Request $request , $customer_id)
    {
        try{

            if(!$request->has('spinner_id') && !$request->has('spin_id')){
                $this->response['message'] = "Gift Item out of stocks.";
                return $this->errorResponse($this->response);
            }
            

            $spin = App\SpinWinner::whereNull('spin_at')
                                    ->where('customer_id' , $customer_id)
                                    ->where('spinner_id' , $request->input('spinner_id'))
                                    ->where('id' , $request->input('spin_id'))
                                    ->first();

            if(!isset($spin)){
                $this->response['message'] = "Gift spin already performed.";
                return $this->errorResponse($this->response);
            }

            if(isset($spin->code)){
                $item = App\SpinGiftItem::where('code', $spin->code)
                                            ->where('gift_id', $spin->gift_id)
                                            ->first();
                if (isset($item)) {
                    $item->update(['gifted_at' => Carbon::now()]);
                }
            }

            // if($request->input('gift_id')) {
            //     $item = App\SpinGiftItem::where('gift_id', $request->input('gift_id'))
            //                                 ->whereDate('gift_at' , Carbon::now())
            //                                 ->where('status', true)
            //                                 ->whereNull('gifted_at')
            //                                 ->first();

            //     if(!isset($item)){
            //         $this->response['message'] = "Gift Item out of stocks.";
            //         return $this->errorResponse($this->response);
            //     }
            //     $item->update(['gifted_at' => Carbon::now()]);
            //     $spin->update(['spin_at' => Carbon::now() ,'is_winner' => 1 , 'code' => $item->code, 'gift_id' => $request->input('gift_id')]);

            // } 

            if(isset($spin)){
                $spin->update(['spin_at' => Carbon::now()]);
            }

            
            $this->response["data"] = $spin;

            // if(isset($spin)){
            //     if($spin->code){
            //         event(new SpunWinner($spin));
            //     }                  
            // }
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function send_gift_nofity($spin_id){
        try{
            $spin = App\SpinWinner::find($spin_id);
            
            if($spin->spin_at == null){
                $spin->update(['spin_at' => Carbon::now()]);
            }
            
            $this->response["data"] = $spin;

            if(isset($spin)){
                if($spin->code){
                    event(new SpunWinner($spin));
                }                  
            }
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function accepted(Request $request)
    {
        try{
            if(!$request->has('token')){
                return $this->errorResponse($this->response);
            }
            $spin_request = App\SpinWinnerRequest::where('token', $request->input('token'))->first();

            if(!isset($spin_request)){
                return $this->errorResponse($this->response);
            }
            $spins = $spin_request->spinners;
            for ($i=0; $i < $spins; $i++) { 
               App\SpinWinner::create([
                   'code' => str_random(32),
                   'spinner_id' => $spin_request->spinner_id,
                   'purchase_id' => $spin_request->purchase_id,
                   'customer_id' => $spin_request->customer_id,
                   'is_winner' => false,
                   'status' => true,
               ]);
            }

            $spin_request->update(['accepted_at' => Carbon::now()]);
            $this->response["data"] = $spin_request;
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function destroy($id)
    {
        try{
            $spin = App\SpinWinner::find($id);
            if(!isset($spin)){
                $this->response['message'] = "Spin Winner not found.!.";
            }
            $spin->update(['status' => false]);

            $this->response["message"] = "Spin Winner remove successfully.!";
            $this->response["data"] = $spin->with(['admin']);
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function customer_spuns(Request $request , $page = 1)
    {     
        try {    
            $spinners = App\SpinWinner::where('status' , true); 

            if($request->has('spinner_id')){
                $spinners = $spinners->where('spinner_id' , $request->input('spinner_id'));
            }

            if($request->has('customer_id')){
                $spinners = $spinners->where('customer_id' , $request->input('customer_id'));
            }

            $this->response["totalItems"] = $spinners->count();
            $this->response['data'] = $spinners->with(['Customer' , 'Spinner'])
                                                ->latest()
                                                ->offset($this->filter["itemPerPage"] * ($page - 1))
                                                ->limit($this->filter["itemPerPage"])
                                                ->get();
            $this->response["currentPage"] = $page;
            $this->response["filter"] = $this->filter;
            $this->response['user'] = Auth::user();
            
            return $this->successResponse($this->response);
        } catch (\Exception $e){
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }
}