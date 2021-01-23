<?php

namespace App\Mail;

use App\Country;
use App\Purchase;
use App\Customer;
use Carbon\Carbon;
use App\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CustomerRegisterEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $customer;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Customer $customer)
    {
       $this->customer = $customer;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = Customer::where('id' , $this->customer->id)->first();
        $country = Country::where('iso' , $user->country_iso)->first();
        //$purchase_amount = Purchase::where('customer_id' , $this->customer->id)->where('status' , true)->sum('amount');

        $settings = SiteSetting::where('status' , true)->get()->pluck('value' , 'key');

        if(isset($settings['send_email'])){

            if($settings['send_email'] && isset($user)){
                $template = isset($settings['email']) ? $settings['email'] : null;
                $keys = ["{{fullname}}" , "{{email}}" , "{{mobile}}" , "{{cpr}}" , "{{nationality}}" , "{{created_date}}"];
                $replace = [$user->fullname , $user->email , $user->mobile , $user->cpr , isset($country) ? $country->name : "" , Carbon::now()->isoFormat('MMMM Do YYYY, h:mm:ss a')];
                $template = str_replace($keys, $replace , $template);

                return $this->from('promo@dragoncitybahrain.com' , 'Dragon City Bahrain')
                            ->subject("Dragon City Bahrain - Summer campaign 2019")
                            ->html($template);
            }

        }

        return null;

        
    }
}
