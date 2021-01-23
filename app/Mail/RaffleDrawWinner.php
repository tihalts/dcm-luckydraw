<?php

namespace App\Mail;

use App\Winner;
use App\Customer;
use App\LuckyDraw;
use App\CampaignTemplate;
use App\RaffleDrawSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use PDF;

class RaffleDrawWinner extends Mailable
{
    use Queueable, SerializesModels;
    public $winner;
    public $template;
    public $email;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($winner, $email)
    {
        $this->winner = $winner;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if(isset($this->winner)){
            $code = $this->winner->uuid;
            $user = Customer::where('id' , $this->winner->customer_id)->first();
            $raffle_tempate = RaffleDrawSetting::where('lucky_draw_id' , $this->winner->lucky_draw_id)->first();
            $luckydraw = LuckyDraw::where('id' , $this->winner->lucky_draw_id)->first();
            $template = isset($raffle_tempate) ? $raffle_tempate->sms : "";
            $template = str_replace("{{code}}",$code,$template);
            $template = str_replace("{{name}}",$luckydraw->name,$template);
            $template = str_replace("{{fullname}}",$user->fullname,$template);
            
            if(isset($code) && isset($template) && !isset($this->email)){           
                return $this->from('promo@dragoncitybahrain.com' , 'Dragon City Bahrain')
                            ->subject($luckydraw->name)
                            ->to($user)
                            ->html($template);
            }
            
            if(isset($this->email)){
                $pdf = PDF::loadView('exports.raffledraw', ["customer" => $user, "winner" => $this->winner, "lucky_draw" => $luckydraw]);
                return $this->from('promo@dragoncitybahrain.com' , 'Dragon City Bahrain')
                            ->subject($luckydraw->name)
                            ->to($this->email)
                            ->view('emails.raffledraw')
                            ->with(['winner' => $this->winner , 'customer' => $user , 'lucky_draw' =>  $luckydraw])
                            ->attachData($pdf->output(), $luckydraw->name . '.pdf');
            }
               
        }
        
    }
}
