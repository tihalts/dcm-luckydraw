<?php
namespace App\Imports;

use App\Shop;
use App\Gift;
use App\Country;
use App\Campaign;
use App\GiftItem;
use App\Customer;
use App\Purchase;
use App\ScratchCard;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LossDataImport implements ToCollection , WithHeadingRow
{
    var $created_at = null;
    var $updated_at = null;

    public function __construct() { 
    }

    public function collection(Collection $rows)
    {
        $data = [];
        $time = 0;
        foreach ($rows as $row) 
        {
            //id	first_name	last_name	cpr	email	phone_code	mobile	
            //dob	country_iso	nationality	created_at	updated_at	status	
            //shop_1	shop1_amount	shop_2	shop2_amount	shop_3	shop3_amount	
            //shop_4	shop4_amount	shop_5	shop5_amount	shop_6	shop6_amount	
            //shop_7	shop7_amount	shop_8	shop8_amount
            $data['first_name'] = $row['first_name'];
            $data['last_name'] = $row['last_name'];
            $data['cpr'] = $row['cpr'];
            $data['email'] = $row['email'];
            $data['phone_code'] = $row['phone_code'];
            $data['mobile'] = $row['mobile'];
            $data['dob'] = $row['dob'];
            $data['country_iso'] = $row['country_iso'];
            $data['nationality'] = $row['nationality']; 
            $data['provider'] = $row['provider']; 
            
            if(isset($data['provider'])){
                $data['provider'] = $data['provider'] == 41 ? $data['provider'] : 26;
            }
            
            $data['purchases'] = [];

            for($i = 0 ; $i < 8; $i++){
                if(!empty($row['shop_' . ($i+1)]) && !empty($row['shop'. ($i+1) .'_amount'])){
                    $data['purchases'][$i]['shop_id'] = $row['shop_'. ($i+1)];
                    $data['purchases'][$i]['amount'] = $row['shop'. ($i+1) .'_amount'];
                 }
            }


            $this->created_at = Carbon::parse('2020-11-28 14:25:00')->addMinutes($time);
            $this->updated_at = Carbon::parse('2020-11-28 14:25:00')->addMinutes($time);

            $time += 2;

            $this->purchase($data);

        }

        return $data;
    }

    public function transformDate($value, $format = 'Y-m-d')
    {
        try {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }

    public function purchase($data)
    {
        if(isset($data['cpr'])){
            $user = Customer::where('cpr' , $data['cpr'])->first();
        }

        try {
            $country = Country::where('iso' , $data['country_iso'])->first();

            if(!isset($user)){     
                $user = new Customer();               
                $user->password = Hash::make(Str::random(8));
                $user->created_user_id = $data['provider'];
            }
            
            $user->first_name = $data['first_name'];
            $user->last_name = $data['last_name'];
            $user->email = $data['email'];
            $user->mobile = $data['mobile'];
            $user->cpr = $data['cpr'];
            $user->country_iso = $data['country_iso']; 
            $user->phone_code = $country->phone_code;
            $user->nationality = $data['nationality'];
            $user->updated_at = $this->updated_at;
            $user->created_at =$this->created_at;
            $user->save();

            foreach ($data['purchases'] as  $value) {
                if(!isset($value['shop_id']) && !isset($value['amount'])) continue;
                if($value['amount'] <= 0) continue;

                $shop = Shop::where('shop_no', $value['shop_id'])->first();
                if(!isset($shop)) continue;
                $purchase = new Purchase();
                $purchase->purchase_no = Str::random(3).mt_rand(100000,999999);
                $purchase->amount = $value['amount'];
                $purchase->customer_id = $user->id;
                $purchase->points = intval($value['amount']);
                $purchase->user_id = $data['provider'];
                $purchase->shop_id = $shop->id;
                $purchase->updated_at = $this->updated_at;
                $purchase->created_at = $this->created_at;
                $purchase->save();
            }

            // $auth_user = Auth::user();
            // $auth_user->updateCustomerId($user->id);
            // event(new CreatePurchase($user , $auth_user));
            $this->ScratchCardCampaigns($user->id, $data['provider']);           

            
        } catch (\Exception $e) {
            $this->response['error_message'] = $e->getMessage();
        }
    }
    
    public function ScratchCardCampaigns($id , $provider_id)
    {
       
        $campaigns = Campaign::where('id', 42)
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
                                'scratched_at' => $this->updated_at,
                                'created_at' => $this->created_at,
                                'updated_at' => $this->updated_at,
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
        $remaining_today_cards = $data['day_limit'] - ScratchCard::whereDate('created_at' , "2020-11-28")->where('status' , true)->count();
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
        
        $probability = 10;//isset($campaign->data['probability']) ? $campaign->data['probability'] : 10;
        $item = $this->rand_gift($campaign_id , $probability);
        $gift = null;
        if(isset($item)){
            $gift = Gift::find($item->gift_id);
            $scarch_card->update([
                'code' => $item->code,
                'gift_id' => isset($gift) ? $gift->id : null,
                'is_winner' => isset($gift) ? true : false,
                'updated_at' => $this->updated_at
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
                                    ->whereDate('gift_at' , "2020-11-28")
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
            $item->update(['gifted_at' => "2020-11-28"]);
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

    public function headingRow(): int
    {
        return 1;
    }
}