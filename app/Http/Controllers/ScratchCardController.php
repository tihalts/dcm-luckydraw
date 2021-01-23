<?php 

namespace App\Http\Controllers;

use App;
use Lang;
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
use Illuminate\Support\Facades\Validator;


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
            $scratchcards = App\ScratchCard::where('status' , true); 

            if($request->has('campaign_id')){
                $scratchcards = $scratchcards->where('campaign_id' , $request->has('campaign_id'));
            }

            if($request->has('customer_id')){
                $scratchcards = $scratchcards->where('customer_id' , $request->has('customer_id'));
            }

            $this->response["totalItems"] = $scratchcards->count();
            $this->response['data'] = $scratchcards->with(['Customer' , 'Gift' , 'Campaign' , 'Provider'])
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
            $scratchcards = App\ScratchCard::where('status' , true);

            if($request->has('campaign_id')){
                $scratchcards = $scratchcards->where('campaign_id' , $request->has('campaign_id'));
            }

            if(isset($filter['customer_id'])){
                $scratchcards = $scratchcards->where('customer_id' , $filter['customer_id']);
            }

            if(isset($filter['promoter_id'])){
                $scratchcards = $scratchcards->where('user_id' , $filter['promoter_id']);
            }


            if(isset($filter['searchText'])){

                $scratchcards = $scratchcards->where('code' , 'LIKE' , '%'.$filter['searchText'].'%')
                                       ->orWhereHas('customer' , function($query) use($filter){
                                            $query->where(function($query) use($filter){
                                                $query->where('first_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('last_name' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('mobile' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('cpr' , 'LIKE' , '%'.$filter['searchText'].'%')
                                                        ->orWhere('email' , 'LIKE' , '%'.$filter['searchText'].'%');
                                            });
                                       })->orWhereHas('Campaign' , function($query) use($filter){
                                            $query->where(function($query) use($filter){
                                                $query->where('name' , 'LIKE' , '%'.$filter['searchText'].'%');
                                            });
                                       });
            }    


            if(isset($filter['start_date']) && isset($filter['end_date'])){
                $start_date = Carbon::parse($filter['start_date'])->startOfDay();
                $end_date = Carbon::parse($filter['end_date'])->endOfDay();
                $scratchcards = $scratchcards->whereDate('created_at' , '>=' , $start_date)
                                                 ->whereDate('created_at' , '<=' , $end_date);
            }
            
            $this->response["totalItems"] = $scratchcards->count();

            if(isset($filter['filter_by'])){
                $scratchcards = $scratchcards->orderBy($filter['filter_by'] , isset($filter["orderby"]) ? $filter["orderby"] : $this->filter["orderby"]);
            } else{
                $scratchcards = $scratchcards->orderBy('id' , 'desc');
            }

            $this->response['data'] = $scratchcards->with(['Customer' ,'Gift' , 'Campaign' , 'Provider'])
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

    public function store(Request $request)
    {
       
    }

    public function unredeemed_cards($customer_id)
    {
        $scarch_cards = App\ScratchCard::whereHas('Campaign' , function($query){
                                            $query->whereDate('start_at' , '<=' , Carbon::now())
                                                  ->whereDate('end_at' , '>=' , Carbon::now())
                                                  ->where('status' , true);
                                        })
                                        // ->whereHas('Campaign.Gifts.Items' , function($query){
                                        //     // $query->whereNull('gift_items.gifted_at')
                                        //     //       ->whereDate('gift_items.gift_at' , Carbon::now())
                                        //     //       ->where('gift_items.status' , true);
                                        // })
                                        ->where('customer_id' , $customer_id)
                                        ->whereNull('scratched_at')
                                        ->get();

        $card_ids = [];
        
        if(isset($scarch_cards))
        {
            foreach($scarch_cards as $card){
                $card_ids[] = $card->id;
                //$this->suffleGifts($card);
            }
        }
         
        

        $this->response["data"] = isset($card_ids) ? $this->scratchCardInfos($card_ids) : [];
            
        return $this->successResponse($this->response);
    }
    
    public function today_gifts($customer_id)
    {
        $scarch_cards = App\ScratchCard::whereHas('Campaign' , function($query){
                                            $query->whereDate('start_at' , '<=' , Carbon::now())
                                                ->whereDate('end_at' , '>=' , Carbon::now())
                                                ->where('status' , true);
                                        })
                                        // ->whereHas('Campaign.Gifts.Items' , function($query){
                                        //     // $query->whereNull('gift_items.gifted_at')
                                        //     //       ->whereDate('gift_items.gift_at' , Carbon::now())
                                        //     //       ->where('gift_items.status' , true);
                                        // })
                                        ->whereDate("created_at" , Carbon::now())
                                        ->where('is_winner' , 1)
                                        ->where('is_gifted' , 0)
                                        ->where('customer_id' , $customer_id)
                                        ->whereNotNull('scratched_at')
                                        ->get();

        $card_ids = [];

        if(isset($scarch_cards))
        {
            foreach($scarch_cards as $card){
                $card_ids[] = $card->id;
                //$this->suffleGifts($card);
            }
        }



        $this->response["data"] = isset($card_ids) ? $this->scratchCardInfos($card_ids) : [];

        return $this->successResponse($this->response);
    }

    public function cardinfos(Request $request , $customer_id)
    {
        if($request->has("cards")){
            $cards = is_array($request->input("cards")) ? $request->input("cards") : [] ;
            $this->response["data"] = isset($cards) ? $this->scratchCardInfos($cards) : [];
            return $this->successResponse($this->response);
        } 
        $this->response["data"] = [];
        return $this->successResponse($this->response);
    }

    public function scratchCardInfos($card_ids)
    {
        $cards = [];
        $scarch_cards = App\ScratchCard::with(['gift' , 'campaign'])->whereIn('id' , $card_ids)->get();
        foreach ($scarch_cards as $card) {
            $gift = $card->gift;
            $campaign = $card->campaign;
            $bg_url = $campaign->data['looser_image'] ? asset('storage/'.$campaign->data['looser_image']) : null;
            $fg_url = $campaign->data['background_image'] ? asset('storage/'.$campaign->data['background_image']) : null;
            $cards[] = [
                'id' => $card->id,
                'campaign_name' => $campaign->name,
                'campaign_id' => $card->campaign_id,
                'customer_id' => $card->customer_id,
                'is_winner' => $card->is_winner,
                'is_scratched' => isset($card->scratched_at) ? true : false,
                'bg_url' => isset($gift->image) ? asset('storage/'.$gift->image) : $bg_url,
                "fg_url" => $fg_url,
            ];
        }
        return $cards;
    }

    public function show($id)
    {
    }

    public function suffleGifts($scarch_card)
    {
        $campaign_id = $scarch_card->campaign_id;
        //$campaign = App\Campaign::where('id' , $scarch_card->campaign_id)->first();
        // $gift_count = App\GiftItem::whereHas('Gift' , function($query) use($campaign_id){
        //                                 $query->where('campaign_id' , $campaign_id);
        //                             })
        //                             ->whereNotNull('gifted_at')
        //                             ->whereDate('gift_at' , Carbon::now())
        //                             ->where('status' , true)
        //                             ->count();
        
        $item = $this->rand_gift($campaign_id);
        $gift = null;
        if(isset($item)){
            $gift = App\Gift::find($item->gift_id);
            $scarch_card->update([
                'code' => $item->code,
                'gift_id' => isset($gift) ? $gift->id : null,
                'is_winner' => isset($gift) ? true : false
            ]);
        }
        
        return $gift;
    }

    public function rand_gift($campaign_id)
    {
        $gift_codes = App\GiftItem::whereHas('Gift' , function($query) use($campaign_id){
                                        $query->where('campaign_id' , $campaign_id);
                                    })
                                    ->whereNull('gifted_at')
                                    ->whereDate('gift_at' , Carbon::now())
                                    ->where('status' , true)
                                    ->orderByRaw('RAND()')
                                    ->first();

        if(!isset($gift_codes->code)){
            return null;
        }
        
        $generated_codes = $this->generated_codes();
        $generated_codes[] = $gift_codes->code;
        $codes = $generated_codes;        
        $codes = collect($codes)->shuffle();        
        $code = $codes->random();
        // $old_item = App\GiftItem::whereHas('Gift' , function($query) use($campaign_id){
        //                                 $query->where('campaign_id' , $campaign_id);
        //                             })
        //                             ->whereNotNull('gifted_at')
        //                             ->where('code' , $code)
        //                             ->first();
        // if(isset($old_item)){
        //     self::rand_gift($campaign_id);
        // }
        $item =  App\GiftItem::whereHas('Gift' , function($query) use($campaign_id){
                                    $query->where('campaign_id' , $campaign_id);
                                })
                                ->whereNull('gifted_at')
                                ->where('code' , $code)
                                ->first();

        if(isset($item)){
            $item->update(['gifted_at' => Carbon::now()]);
        }
        
        return isset($item) ? $item : null;
    }

    public function generated_codes()
    {
        
        $codes = [];
        for ($i=0; $i < 5 ; $i++) { 
            $codes[] = str_random(32);
        }
        return $codes;
    }

    public function getItems($campaign_id = null)
    {
        $this->day_settings = config('scarch_card_settings');
        $gift_items = App\GiftItem::whereHas('Gift' , function($query) use($campaign_id){
                                        $query->where('campaign_id' , $campaign_id);
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
            $ScratchCard = App\ScratchCard::find($id);
            $this->response["data"] = $ScratchCard;
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function scratched($card_id)
    {
        try{
            $card = App\ScratchCard::find($card_id);
            $card->update(['scratched_at' => Carbon::now()]);
            $this->response["data"] = $card;

            if(isset($card)){
                if($card->code){
                    event(new ScratchCardWinner($card));
                }                  
            }
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function skipcards($customer_id)
    {
        try{
            $scarch_cards = App\ScratchCard::where('customer_id' , $customer_id)
                                            ->whereNull('scratched_at')
                                            ->get();

            if(isset($scarch_cards)) {
                foreach ($scarch_cards as $scarch_card) {

                    if($scarch_card->code){
                        event(new ScratchCardWinner($scarch_card));
                    }  
                    
                    $scarch_card->update(['scratched_at' => Carbon::now()]);
                }
                
            }
              
            $this->response["data"] = $scarch_cards;
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function send_gift_nofity($card_id){
        try{
            $card = App\ScratchCard::find($card_id);
            
            if($card->scratched_at == null){
                $card->update(['scratched_at' => Carbon::now()]);
            }
            
            $this->response["data"] = $card;

            if(isset($card)){
                if($card->code){
                    event(new ScratchCardWinner($card));
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
            $card_request = App\ScratchCardRequest::where('token', $request->input('token'))->first();

            if(!isset($card_request)){
                return $this->errorResponse($this->response);
            }
            $cards = $card_request->scratch_cards;
            for ($i=0; $i < $cards; $i++) { 
               App\ScratchCard::create([
                   'code' => str_random(32),
                   'campaign_id' => $card_request->campaign_id,
                   'purchase_id' => $card_request->purchase_id,
                   'customer_id' => $card_request->customer_id,
                   'is_winner' => false,
                   'status' => true,
               ]);
            }

            $card_request->update(['accepted_at' => Carbon::now()]);
            $this->response["data"] = $card_request;
            
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function update(Request $request, $company_id , $id)
    {
        
    }

    public function destroy($id)
    {
        try{
            $card = App\Voucher::find($id);
            if(!isset($card)){
                $this->response['message'] = "Voucher not found.!.";
            }
            $card->update(['status' => false]);

            $this->response["message"] = "Voucher remove successfully.!";
            $this->response["data"] = $card->with(['admin']);
            return $this->successResponse($this->response);
        }catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
            return $this->errorResponse($this->response);
        }
    }

    public function customer_cards(Request $request , $page = 1)
    {     
        try {    
            $scratchcards = App\ScratchCard::where('status' , true); 

            if($request->has('campaign_id')){
                $scratchcards = $scratchcards->where('campaign_id' , $request->input('campaign_id'));
            }

            if($request->has('customer_id')){
                $scratchcards = $scratchcards->where('customer_id' , $request->input('customer_id'));
            }

            $this->response["totalItems"] = $scratchcards->count();
            $this->response['data'] = $scratchcards->with(['Customer' , 'Campaign'])
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

    public function ScratchCards($customer_id)
    {
        $campaign = App\Campaign::find(2);
        $scratch_cards = 0; 
        if(!isset($campaign)){
            return $scratch_cards;
        }
        
        $start_at = $campaign->start_at ? $campaign->start_at : Carbon::now();
        $end_at = $campaign->end_at ? $campaign->end_at : Carbon::now();

        $purchase_amount = App\Purchase::whereBetween('created_at' ,[$start_at , $end_at])->where('customer_id' ,$customer_id)->where('status' , true)->sum('amount');
        $claim_cards = App\ScratchCard::where('campaign_id' , $campaign->id)->where('customer_id' ,$customer_id)->where('status' , true)->count();
        $data = $campaign->data;
        if($purchase_amount <= 0 || $data['amount'] <= 0){
            return $scratch_cards;
        }

        $remaining_customer_cards = $data['customer_limit'] - App\ScratchCard::where('campaign_id' , $campaign->id)->where('customer_id' ,$customer_id)->where('status' , true)->count();
        $remaining_today_cards = $data['day_limit'] - App\ScratchCard::whereDate('created_at' , Carbon::now())->where('status' , true)->count();
        $remaining_campaign_cards = $data['max_limit'] - App\ScratchCard::where('campaign_id' , $campaign->id)->where('status' , true)->count();   
        
        if($remaining_customer_cards <= 0 && $data['customer_limit'] != 0){
            return $scratch_cards;
        }

        if($remaining_today_cards <= 0 && $data['day_limit'] != 0){
            return $scratch_cards;
        }

        if($remaining_campaign_cards <= 0 && $data['max_limit'] != 0){
            return $scratch_cards;
        }

        $customer_remaining_campaign_amount = $purchase_amount - ($claim_cards * $data['amount']);

        if($customer_remaining_campaign_amount <= $data['amount']){
            return $scratch_cards;
        }        

        if($data['amount'] != 0){
            $scratch_cards = floor($customer_remaining_campaign_amount / $data['amount']);
        }        
        
        if($data['customer_limit'] != 0){
            if($remaining_customer_cards < $scratch_cards){
                $scratch_cards = $remaining_customer_cards;
            }
        }

        if($data['day_limit'] != 0){
            if($remaining_today_cards < $scratch_cards){
                $scratch_cards = $remaining_today_cards;
            }
        }

        if($data['max_limit'] != 0){
            if($remaining_campaign_cards < $scratch_cards){
                $scratch_cards = $remaining_campaign_cards;
            }  
        }

        if($scratch_cards < 0){
            $scratch_cards = 0; 
        }

        return $scratch_cards;
    }
}