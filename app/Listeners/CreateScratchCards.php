<?php

namespace App\Listeners;

use App\Gift;
use App\User;
use Newsletter;
use App\Country;
use App\GiftItem;
use App\Customer;
use App\Purchase;
use App\Campaign;
use Carbon\Carbon;
use App\ScratchCard;
use App\LuckyDrawPoint;
use App\ScratchCardRequest;
use App\Mail\PurchaseEmail;
use Illuminate\Support\Str;
use App\Events\CreatePurchase;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateScratchCards
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  CreatePurchase  $event
     * @return void
     */
    public function handle(CreatePurchase $event)
    {
        $user = Customer::where('id' , $event->customer->id)->first();
        $provider = User::where('id' , $event->user->id)->first();
        $this->ScratchCardCampaigns($user->id , $provider->id);
    }

    public function ScratchCardCampaigns($id , $provider_id)
    {
       
        $campaigns = Campaign::whereDate('start_at' , '<=' , Carbon::now())
                              ->whereDate('end_at' , '>=' , Carbon::now())
                            //   ->whereHas('Gifts.Items' , function($query){
                            //         $query->whereNull('gift_items.gifted_at')
                            //             ->whereDate('gift_items.gift_at' , Carbon::now())
                            //             ->where('gift_items.status' , true);
                            //     })
                                ->get();

        foreach ($campaigns as $campaign) {
            $scratch_cards = $this->ScratchCards($campaign , $id);
            if($scratch_cards > 0){
                // $card_request = ScratchCardRequest::create([
                //     'campaign_id' => $campaign->id ,
                //     'customer_id' => $id ,
                //     'token' => str_random(32),
                //     'scratch_cards' => $scratch_cards,
                //     'status' => true
                // ]);

                ScratchCard::where("customer_id" , $id)->update([ "is_gifted" => true]);

                for ($i=0; $i < $scratch_cards; $i++) { 
                    $card = ScratchCard::create([
                                'campaign_id' => $campaign->id,
                                'customer_id' => $id,
                                'is_winner' => false,
                                'user_id' => $provider_id,
                                'is_gifted' => false,
                                'status' => true,
                            ]);

                    $this->suffleGifts($card);
                }
                //$card_request->update(['accepted_at' => Carbon::now()]);
            }
        }
    }

    public function ScratchCards(Campaign $campaign , $customer_id)
    {
        $scratch_cards = 0; 
        if(!isset($campaign)){
            return $scratch_cards;
        }
        
        $start_at = $campaign->start_at ? $campaign->start_at : Carbon::now();
        $end_at = $campaign->end_at ? $campaign->end_at : Carbon::now();

        $purchase_amount = Purchase::whereBetween('created_at' ,[$start_at , $end_at])->where('customer_id' ,$customer_id)->where('status' , true)->sum('amount');
        $claim_cards = ScratchCard::where('campaign_id' , $campaign->id)->where('customer_id' ,$customer_id)->where('status' , true)->count();
        $data = $campaign->data;
        if($purchase_amount <= 0 || $data['amount'] <= 0){
            return $scratch_cards;
        }

        $remaining_customer_cards = $data['customer_limit'] - ScratchCard::where('campaign_id' , $campaign->id)->where('customer_id' ,$customer_id)->where('status' , true)->count();
        $remaining_today_cards = $data['day_limit'] - ScratchCard::whereDate('created_at' , Carbon::now())->where('status' , true)->count();
        $remaining_campaign_cards = $data['max_limit'] - ScratchCard::where('campaign_id' , $campaign->id)->where('status' , true)->count();   
        
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

        if($customer_remaining_campaign_amount < $data['amount']){
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

    public function suffleGifts($scarch_card)
    {
        $campaign_id = $scarch_card->campaign_id;
        $campaign = Campaign::where('id' , $scarch_card->campaign_id)->first();
        // $gift_count = App\GiftItem::whereHas('Gift' , function($query) use($campaign_id){
        //                                 $query->where('campaign_id' , $campaign_id);
        //                             })
        //                             ->whereNotNull('gifted_at')
        //                             ->whereDate('gift_at' , Carbon::now())
        //                             ->where('status' , true)
        //                             ->count();
        
        $probability = isset($campaign->data['probability']) ? $campaign->data['probability'] : 10;
        $item = $this->rand_gift($campaign_id , $probability);
        $gift = null;
        if(isset($item)){
            $gift = Gift::find($item->gift_id);
            $scarch_card->update([
                'code' => $item->code,
                'gift_id' => isset($gift) ? $gift->id : null,
                'is_winner' => isset($gift) ? true : false
            ]);
        }
        
        return $gift;
    }

    public function rand_gift($campaign_id , $probability = 10)
    {
        $gift_codes = GiftItem::whereHas('Gift' , function($query) use($campaign_id){
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
        
        $generated_codes = $this->generated_codes($probability);
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
        $item =  GiftItem::whereHas('Gift' , function($query) use($campaign_id){
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

    public function generated_codes($probability = 10)
    {
        
        $codes = [];
        for ($i=0; $i < $probability ; $i++) { 
            $codes[] = str_random(32);
        }
        return $codes;
    }
}
