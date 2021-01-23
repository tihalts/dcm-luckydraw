<?php

namespace App\Listeners;

use App\Gift;
use App\Customer;
use App\Campaign;
use Carbon\Carbon;
use App\CampaignTemplate;
use App\Jobs\ScratchCardEmailJob;
use App\Events\ScratchCardWinner;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\ScratchCardWinner as WinnerMail;

class ScratchCardWinnerEmailNotification
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
     * @param  ScratchCardWinner  $event
     * @return void
     */
    public function handle(ScratchCardWinner $event)
    {
        if(isset($event->scratch_card->is_winner) && isset($event->scratch_card->code)){
            $user = Customer::where('id' , $event->scratch_card->customer_id)->first();
            $code = $event->scratch_card->code;
            $scratch_card = $event->scratch_card;
            $campaign =  Campaign::find($event->scratch_card->campaign_id);
            if(isset($code) && $campaign->send_email){
                //Mail::to($user)->send(new WinnerMail($scratch_card));
                ScratchCardEmailJob::dispatch($user, new WinnerMail($scratch_card))->delay(Carbon::now()->addSeconds(4));
            }
        }
    }
}
