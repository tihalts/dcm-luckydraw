<?php

namespace App\Listeners;

use App\Winner;
use App\Customer;
use App\LuckyDraw;
use App\RaffleDrawSetting;
use App\Events\RaffleDraw;
use App\Jobs\RaffleDrawSMSJob;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RaffleDrawSMSNotification
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
        if(isset($event->voucher->code) && !isset($event->email)){
            $user = Customer::where('id' , $event->winner->customer_id)->first();
            $setting = RaffleDrawSetting::where('lucky_draw_id' , $event->winner->lucky_draw_id)->first();

            if(isset($setting)){
                if($setting->send_sms){
                    $luckydraw = LuckyDraw::where('id' , $event->winner->lucky_draw_id)->first();
                    $code = $event->winner->uuid;
                    $template = isset($setting) ? $setting->sms : "";
                    $template = str_replace("{{code}}",$code,$template);
                    $template = str_replace("{{name}}",$luckydraw->name,$template);
                    $template = str_replace("{{fullname}}",$user->fullname,$template);
                    //sendsms($user->mobile , $template , "DragonCity");
                    RaffleDrawSMSJob::dispatch($user, $template);
                }
            }
            
        }
    }
}
