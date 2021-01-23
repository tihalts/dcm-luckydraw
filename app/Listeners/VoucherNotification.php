<?php

namespace App\Listeners;

use App\User;
use App\Country;
use App\Voucher;
use App\Campaign;
use App\Customer;
use App\Purchase;
use Carbon\Carbon;
use App\CampaignTemplate;
use App\ScratchCardRequest;
use App\Events\CreatePurchase;
use Illuminate\Support\Facades\Mail;
use App\Mail\RewardPointVoucher as VoucherMail;
use App\Events\RewardPointVoucher;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class VoucherNotification
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
        $vouchers = 0;
        $campaigns = Campaign::where('status' , true)
                                ->where('campaign_type' , 'reward_point')
                                ->where(function($query){
                                    $query->whereDate('start_at' , '<=' , Carbon::now())
                                        ->whereDate('end_at' , '>=' , Carbon::now());
                                })
                                ->get();

        foreach ($campaigns as $campaign) {
            $vouchers = $this->limit($campaign , $user->id);
            $campain_tempate = CampaignTemplate::where('campaign_id' , $campaign->id)->first();

            if(isset($campain_tempate->sms) && $vouchers != 0 && $campaign->send_sms){
                $smstemplate = $campain_tempate->sms;
                $smstemplate = str_replace("{{vouchers}}",$vouchers,$smstemplate);
                $temsmstemplateplate = str_replace("{{name}}",$campaign->name,$smstemplate);
                sendsms($user->mobile , $smstemplate , "DragonCity");
            }

            if(isset($campain_tempate->email)  && $vouchers != 0 && $campaign->send_email){
                $emailtemplate = $campain_tempate->email;
                $emailtemplate = str_replace("{{vouchers}}",$vouchers,$emailtemplate);
                $emailtemplate = str_replace("{{name}}",$campaign->name,$emailtemplate);
                Mail::to($user->email)->send(new VoucherMail($emailtemplate));
            }
        }
    }

    public function limit($campaign , $customer_id)
    {
        $voucher_count = 0; 
        if(!isset($campaign)){
            return $voucher_count;
        }
        $data = $campaign->data;
        $campaign_id = $campaign->id;

        $remaining_customer_vouchers = $data['customer_limit'] - Voucher::where('campaign_id' , $campaign_id)->where('customer_id' ,$customer_id)->where('status' , true)->count();
        $remaining_today_vouchers = $data['day_limit'] - Voucher::whereDate('created_at' , Carbon::now())->where('status' , true)->count();
        $remaining_campaign_vouchers = $data['max_limit'] - Voucher::where('campaign_id' , $campaign_id)->where('status' , true)->count();    
       
        

        $start_at = $campaign->start_at ? $campaign->start_at : Carbon::now();
        $end_at = $campaign->end_at ? $campaign->end_at : Carbon::now();

        $purchase_amount = Purchase::whereBetween('created_at' ,[$start_at , $end_at])->where('customer_id' ,$customer_id)->where('status' , true)->sum('amount');
        $voucher_amount = Voucher::where('campaign_id' , $campaign_id)->where('customer_id' ,$customer_id)->where('status' , true)->sum('voucher_amount');

        if($remaining_customer_vouchers <= 0 && $data['customer_limit'] != 0){
            return $voucher_count;
        }

        if($remaining_today_vouchers <= 0 && $data['day_limit'] != 0){
            return $voucher_count;
        }

        if($remaining_campaign_vouchers <= 0 && $data['max_limit'] != 0){
            return $voucher_count;
        }
        
        $customer_remaining_campaign_amount = $purchase_amount - $voucher_amount;

        if($customer_remaining_campaign_amount < $data['amount']){
            return $voucher_count;
        }

        if($data['amount'] != 0){
            $voucher_count = floor($customer_remaining_campaign_amount / $data['amount']);
        } 
        
        if($data['customer_limit'] != 0){
            if($remaining_customer_vouchers < $voucher_count){
                $voucher_count = $remaining_customer_vouchers;
            }
        }
        
        if($data['day_limit'] != 0){
            if($remaining_today_vouchers < $voucher_count){
                $voucher_count = $remaining_today_vouchers;
            }
        }

        if($data['max_limit'] != 0){
            if($remaining_campaign_vouchers < $voucher_count){
                $voucher_count = $remaining_campaign_vouchers;
            }  
        }
        
        if($voucher_count < 0){
            $voucher_count = 0; 
        }

        return $voucher_count;
    }
}
