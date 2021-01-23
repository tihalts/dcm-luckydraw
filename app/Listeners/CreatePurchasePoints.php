<?php

namespace App\Listeners;

use App\User;
use Newsletter;
use App\Country;
use App\Customer;
use App\Purchase;
use App\Campaign;
use App\LuckyDraw;
use Carbon\Carbon;
use App\ScratchCard;
use App\LuckyDrawPoint;
use App\RaffleDrawSetting;
use App\ScratchCardRequest;
use App\Mail\PurchaseEmail;
use Illuminate\Support\Str;
use App\Events\CreatePurchase;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreatePurchasePoints
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
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
        $this->CreateLuckyDrawPoints($user->id);        

        
        //Mail::to($user)->subject("Your purchase nptification")->send(new PurchaseEmail($event->purchase));
    }

    public function CreateLuckyDrawPoints($id)
    {
        $raffle_draws =  LuckyDraw::where('status' , true)
                                    ->whereDate('start_at' , '<=' , Carbon::now())
                                    ->whereDate('end_at' , '>=' , Carbon::now())
                                    ->get();

        $points = 0;
        $data = [];
        
        foreach ($raffle_draws as $raffle_draw) {

            $setting = RaffleDrawSetting::where('lucky_draw_id' , $raffle_draw->id)->first();
            $purchase_amount = Purchase::where("customer_id" , $id)
                                    ->whereDate('created_at' , '>=' , $raffle_draw->start_at)
                                    ->whereDate('created_at' , '<=' , $raffle_draw->end_at)
                                    ->where('status' , true)
                                    //->whereDoesntHave('LuckyDrawPoints')
                                    ->sum('amount');
                                          
            //foreach ($purchases as $purchase) {
                $count = LuckyDrawPoint::where("customer_id" , $id)
                                        ->whereDate('created_at' , '>=' , $raffle_draw->start_at)
                                        ->whereDate('created_at' , '<=' , $raffle_draw->end_at)
                                        ->count();

                if($setting->min_amount > 0 && $count > 0){
                    $purchase_amount = $purchase_amount - $count * $setting->min_amount;
                }
                                        
                if(isset($setting) && $purchase_amount > 0){
    
                    $points = $setting->min_amount != 0 ? floor($purchase_amount / $setting->min_amount) : $purchase_amount;                

                    if($setting->max_points != 0){
                        //$points = $setting->max_points >=  $points ? $points : $setting->max_points;
                        $points = ($setting->max_points - ($count + $points)) >= 0  ? $points : $setting->max_points - $count;
                        $points = floor($points);
                    } 

                    if($points < 0) break;
    
                    for ($i=0; $i < $points; $i++) { 
                        $data[$i] = [
                            'uuid' => Str::random(3).mt_rand(10000,99999),
                            'purchase_id' => 0,
                            'customer_id' => $id,
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                            'status' => true
                        ];
                    }
                    
                    if(isset($data)){
                        LuckyDrawPoint::insert($data);
                    }
                    
                    $points = 0;
                    $data = [];
                }
            //}            
        }
    }   
}
