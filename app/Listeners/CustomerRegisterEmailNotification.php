<?php

namespace App\Listeners;

use App\Customer;
use Carbon\Carbon;
use App\Events\CustomerRegister;
use App\Mail\CustomerRegisterEmail;
use Illuminate\Support\Facades\Mail;
use App\Jobs\CustomerRegisterEmailJob;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomerRegisterEmailNotification
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

        //Mail::to($user)->send(new CustomerRegisterEmail($user));
        CustomerRegisterEmailJob::dispatch($user, new CustomerRegisterEmail($user))->delay(Carbon::now()->addSeconds(4));
    }
}
