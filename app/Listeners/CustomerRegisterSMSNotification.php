<?php

namespace App\Listeners;

use App\Customer;
use App\SiteSetting;
use App\Events\CustomerRegister;
use App\Mail\CustomerRegisterEmail;
use App\Jobs\CustomerRegisterSMSJob;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomerRegisterSMSNotification
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
     * @param  CustomerRegister  $event
     * @return void
     */
    public function handle(CustomerRegister $event)
    {
        $user = Customer::where('id' , $event->customer->id)->first();
        $settings = SiteSetting::where('status' , true)->get()->pluck('value' , 'key');

        if(isset($settings['send_sms'])){

            if($settings['send_sms'] && isset($user)){
                $message = "Dear Customer, Thank you for registering at Dragon City. Enjoy Shopping!.";
                //sendsms($user->mobile , 'Dear Customer, Thank you for registering at Dragon City. Enjoy Shopping!.' , "DragonCity");
                CustomerRegisterSMSJob::dispatch($user, $message);
            }

        }
       
    }
}
