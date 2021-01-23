<?php

namespace App\Mail;

use App\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $reset_token = strtolower(str_random(64));
        DB::table('password_resets')->insert([
            'email' => $this->user->email,
            'token' => $reset_token,
            'created_at' => Carbon::now()
        ]);
        return $this->from(config('MAIL_USERNAME') , 'Dragon City Bahrain')
                    ->to($this->user->email)
                    ->subject("Password reset mail.")
                    ->view('emails.reset-email')
                    ->with([
                        'token' => $reset_token, 
                        'user'  => $this->user
                    ]);
    }
}
