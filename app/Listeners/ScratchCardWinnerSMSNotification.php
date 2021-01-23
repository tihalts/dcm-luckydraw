<?php

namespace App\Listeners;

use App\Gift;
use App\Campaign;
use App\Customer;
use App\CampaignTemplate;
use App\Jobs\ScratchCardSMSJob;
use App\Events\ScratchCardWinner;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ScratchCardWinnerSMSNotification
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
            $gift = Gift::find($event->scratch_card->gift_id);
            $campain_tempate = CampaignTemplate::where('campaign_id' , $event->scratch_card->campaign_id)->first();
            $campaign =  Campaign::find($event->scratch_card->campaign_id);
            if(isset($campain_tempate->sms) && isset($code) && isset($gift) && $campaign->send_sms){
                $template = $campain_tempate->sms;
                $template = str_replace("{{code}}",$code,$template);
                $template = str_replace("{{name}}",$gift->name,$template);
                //sendsms($user->mobile , $template , "DragonCity");
                ScratchCardSMSJob::dispatch($user, $template);
            }
        }
       
    }
}
