<?php

namespace App\Listeners;

use App\Spinner;
use App\SpinGift;
use App\Customer;
use Carbon\Carbon;
use App\Events\SpunWinner;
use App\Jobs\SpunEmailJob;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class SpunWinnerEmailNotification
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
            $spun = $event->spun;
            $spinner =  Spinner::find($event->spun->campaign_id);
            if(isset($code) && $spinner->send_email){
                //Mail::to($user)->send(new WinnerMail($spun));
                SpunEmailJob::dispatch($user, new WinnerMail($spun))->delay(Carbon::now()->addSeconds(4));
            }
        }
    }
}
