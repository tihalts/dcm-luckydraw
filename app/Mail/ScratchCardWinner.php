<?php

namespace App\Mail;

use App\Gift;
use App\ScratchCard;
use App\CampaignTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ScratchCardWinner extends Mailable
{
    use Queueable, SerializesModels;
    public $scratch_card;
    public $template;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(ScratchCard $scratch_card)
    {
        $this->scratch_card = $scratch_card;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if(isset($this->scratch_card->is_winner) && isset($this->scratch_card->code)){
            $code = $this->scratch_card->code;
            $campain_tempate = CampaignTemplate::where('campaign_id' , $this->scratch_card->campaign_id)->first();
            //$this->template = isset($campain_tempate) ? $campain_tempate->email : "";
            $gift = Gift::find($this->scratch_card->gift_id);
            // $this->template = str_replace("{{code}}",$code,$this->template);
            // $this->template = str_replace("{{name}}",$gift->name,$this->template);
            // if($this->scratch_card->is_winner) {
            //     return $this->from('promo@dragoncitybahrain.com')
            //                 ->subject("Dragon City Bahrain - Summer campaign 2019")
            //                 ->view('emails.scratch-card' , ['template' => $this->template]);;
            // } 
            $template = isset($campain_tempate) ? $campain_tempate->email : "";
            $template = str_replace("{{code}}",$code,$template);
            $template = str_replace("{{name}}",$gift->name,$template);
            if(isset($code) && isset($template) && isset($gift)){                
                return $this->from('promo@dragoncitybahrain.com' , 'Dragon City Bahrain')
                            ->subject("Dragon City Bahrain - Summer campaign 2019")
                            ->html($template);
            }     
               
        }
        
    }
}
