<?php

namespace App\Jobs;

use App\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Mail\ScratchCardWinner as Mailable;

class SpunEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   /**
     * @var User
     */
    protected $user;

    /**
     * @var Mailable
     */
    protected $mail;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param Mailable $mail
     */
    public function __construct(Customer $user, Mailable $mail)
    {
        $this->user = $user;
        $this->mail = $mail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->user)->send($this->mail);
    }
}
