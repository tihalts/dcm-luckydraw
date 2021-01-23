<?php

namespace App\Mail;

use App\Country;
use App\Purchase;
use App\Customer;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PurchaseEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $purchase;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Purchase $purchase)
    {
        $this->purchase = $purchase;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = Customer::where('id' , $this->purchase->customer_id)->first();
        $country = Country::where('iso' , $user->country_iso)->first();
        return $this->from('example@example.com' , 'Dragon City Bahrain')
                    ->view('emails.purchase')
                    ->with([
                        'FNAME' => $user->first_name , 
                        'LNAME' => $user->last_name, 
                        'PHONE' => $user->mobile, 
                        'EMAIL' => $user->email, 
                        'CPR' => $user->cpr , 
                        'COUNTRY' => isset($country) ? $country->name : "",
                        'AMOUNT' => $this->purchase->amount ,
                        'POINTS' => intval($this->purchase->amount),
                        'CREATEDAT' => Carbon::parse($this->purchase->created_at)->isoFormat('MMMM Do YYYY, h:mm:ss a') ,                        
                    ]);
    }
}
