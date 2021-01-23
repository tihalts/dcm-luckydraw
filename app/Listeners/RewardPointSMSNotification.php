<?php

namespace App\Listeners;

use App\Customer;
use App\CampaignTemplate;
use App\Jobs\RewardPointSMSJob;
use App\Events\RewardPointVoucher;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RewardPointSMSNotification
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
     * @param  RewardPointVoucher  $event
     * @return void
     */
    public function handle(RewardPointVoucher $event)
    {
        if(isset($event->voucher->code)){
            $user = Customer::where('id' , $event->voucher->customer_id)->first();
            $campaign =  Campaign::find($event->voucher->campaign_id);

            if($campaign->send_sms){
                $code = $event->voucher->code;
                $campain_tempate = CampaignTemplate::where('campaign_id' , $event->voucher->campaign_id)->first();
                $template = isset($campain_tempate) ? $campain_tempate->sms : "";
                $template = str_replace("{{code}}",$code,$template);
                //sendsms($user->mobile , $template , "DragonCity");
                RewardPointSMSJob::dispatch($user, $template);
            }
            
        }
    }
}
