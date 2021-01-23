<?php

namespace App\Mail;

use App\Voucher;
use App\CampaignTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class RewardPointVoucher extends Mailable
{
    use Queueable, SerializesModels;
    public $voucher;
    public $template;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($template)
    {
        $this->template = $template;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if(isset( $this->template)){
            return $this->from('promo@dragoncitybahrain.com' , 'Dragon City Bahraind')
                        ->subject("Dragon City Bahrain - Summer campaign 2019")
                        ->html($this->template);
                        //->view('emails.reward-point' , ['template' => $this->template]);     
               
        }
        
    }
}
