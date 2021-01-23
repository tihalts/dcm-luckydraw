<?php

namespace App\Jobs;

use App\Customer;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ScratchCardSMSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var SMS
     */
    protected $message;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param SMS $mail
     */
    public function __construct(Customer $user, $message)
    {
        $this->user = $user;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        sendsms($this->user->mobile , $this->message , "DragonCity");
    }
}
