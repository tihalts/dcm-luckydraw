<?php

namespace App\Listeners;

use App\User;
use Newsletter;
use App\Country;
use App\Spinner;
use App\SpinGift;
use App\Customer;
use App\Purchase;
use Carbon\Carbon;
use App\SpinWinner;
use App\SpinGiftItem;
use App\LuckyDrawPoint;
use App\SpinWinnerRequest;
use App\Mail\PurchaseEmail;
use Illuminate\Support\Str;
use App\Events\CreatePurchase;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateSpinners
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
        $this->Spinners($user->id , $provider->id);
    }

    public function Spinners($id , $provider_id)
    {
       
        $spinners = Spinner::whereDate('start_at' , '<=' , Carbon::now())
                              ->whereDate('end_at' , '>=' , Carbon::now())
                              ->whereHas('Gifts.Items' , function($query){
                                    $query->whereNull('spin_gift_items.gifted_at')
                                        ->whereDate('spin_gift_items.gift_at' , Carbon::now())
                                        ->where('spin_gift_items.status' , true);
                                })
                                ->get();

        foreach ($spinners as $spinner) {
            $spuns = $this->Spuns($spinner , $id);
            if($spuns > 0){

                SpinWinner::where("customer_id" , $id)->update([ "is_gifted" => true]);

                for ($i=0; $i < $spuns; $i++) { 
                    $card = SpinWinner::create([
                                'spinner_id' => $spinner->id,
                                'customer_id' => $id,
                                'is_winner' => false,
                                'user_id' => $provider_id,
                                'is_gifted' => false,
                                'status' => true,
                            ]);
                }
            }
        }
    }

    public function Spuns(Spinner $spinner , $customer_id)
    {
        $spuns = 0; 
        if(!isset($spinner)){
            return $spuns;
        }
        
        $start_at = $spinner->start_at ? $spinner->start_at : Carbon::now();
        $end_at = $spinner->end_at ? $spinner->end_at : Carbon::now();

        $purchase_amount = Purchase::whereBetween('created_at' ,[$start_at , $end_at])->where('customer_id' ,$customer_id)->where('status' , true)->sum('amount');
        $claim_spun = SpinWinner::where('spinner_id' , $spinner->id)->where('customer_id' ,$customer_id)->where('status' , true)->count();
        $data = $spinner->data;
        if($purchase_amount <= 0 || $data['min_amount'] <= 0){
            return $spuns;
        }

        $remaining_customer_spuns = $data['customer_limit'] - SpinWinner::where('spinner_id' , $spinner->id)->where('customer_id' ,$customer_id)->where('status' , true)->count();
        //$remaining_today_spuns = $data['day_limit'] - SpinWinner::whereDate('created_at' , Carbon::now())->where('status' , true)->count();
        //$remaining_spinner_spuns = $data['max_limit'] - SpinWinner::where('spinner_id' , $spinner->id)->where('status' , true)->count();   
        
        if($remaining_customer_spuns <= 0 && $data['customer_limit'] != 0){
            return $spuns;
        }

        // if($remaining_today_spuns <= 0 && $data['day_limit'] != 0){
        //     return $spuns;
        // }

        // if($remaining_spinner_spuns <= 0 && $data['max_limit'] != 0){
        //     return $spuns;
        // }

        $customer_remaining_spinner_amount = $purchase_amount - ($claim_spun * $data['min_amount']);

        if($customer_remaining_spinner_amount < $data['min_amount']){
            return $spuns;
        }        

        if($data['min_amount'] != 0){
            $spuns = floor($customer_remaining_spinner_amount / $data['min_amount']);
        }        
        
        if($data['customer_limit'] != 0){
            if($remaining_customer_spuns < $spuns){
                $spuns = $remaining_customer_spuns;
            }
        }

        // if($data['day_limit'] != 0){
        //     if($remaining_today_spuns < $spuns){
        //         $spuns = $remaining_today_spuns;
        //     }
        // }

        // if($data['max_limit'] != 0){
        //     if($remaining_spinner_spuns < $spuns){
        //         $spuns = $remaining_spinner_spuns;
        //     }  
        // }

        if($spuns < 0){
            $spuns = 0; 
        }

        return $spuns;
    }
}
