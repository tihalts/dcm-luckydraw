<?php

namespace App\Listeners;

use App\Campaign;
use App\Customer;
use App\CampaignTemplate;
use App\Jobs\RewardPointEmailJob;
use App\Events\RewardPointVoucher;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\RewardPointVoucher as VoucherMail;

class RewardPointEmailNotification
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
            $voucher = $event->voucher;
            //Mail::to($user->email)->send(new VoucherMail($voucher));
            $campaign =  Campaign::find($voucher->campaign_id);

            if($campaign->send_email){
                RewardPointEmailJob::dispatch($user, new VoucherMail($voucher))->delay(Carbon::now()->addSeconds(4));
            }
            
        }
    }
}
