<?php

namespace App\Listeners;

use App\Customer;
use Carbon\Carbon;
use App\RaffleDrawSetting;
use App\Events\RaffleDraw;
use App\Mail\RaffleDrawWinner;
use App\Jobs\RaffleDrawEmailJob;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RaffleDrawEmailNotification
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
     * @param  RaffleDraw  $event
     * @return void
     */
    public function handle(RaffleDraw $event)
    {
        if(isset($event->winner)){
            $user = Customer::where('id' , $event->winner->customer_id)->first();
            $winner = $event->winner;
            $setting = RaffleDrawSetting::where('lucky_draw_id' , $winner->lucky_draw_id)->first();
            if(isset($setting)){
                if($setting->send_email || isset($event->email)){
                    //Mail::to($user->email)->send(new RaffleDrawWinner($winner));
                    RaffleDrawEmailJob::dispatch($user, new RaffleDrawWinner($winner , $event->email))->delay(Carbon::now()->addSeconds(4));
                }
            }
            
        }
    }
}
