<?php

namespace App\Listeners;

use App\Spinner;
use App\SpinGift;
use App\Customer;
use App\Jobs\SpunSMSJob;
use App\Events\SpunWinner;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SpunWinnerSMSNotification
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
     * @param  SpunWinner  $event
     * @return void
     */
    public function handle(SpunWinner $event)
    {
        if(isset($event->spun->is_winner) && isset($event->spun->code)){
            $user = Customer::where('id' , $event->spun->customer_id)->first();
            $code = $event->spun->code;
            $gift = SpinGift::find($event->spun->gift_id);
            $spinner =  Spinner::find($event->spun->spinner_id);
            if(isset($spinner->sms) && isset($code) && isset($gift) && $spinner->send_sms){
                $template = $spinner->sms;
                $template = str_replace("{{code}}",$code,$template);
                $template = str_replace("{{name}}",$gift->name,$template);
                //sendsms($user->mobile , $template , "DragonCity");
                SpunMSJob::dispatch($user, $template);
            }
        }
    }
}
