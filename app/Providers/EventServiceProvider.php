<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\CreatePurchase' => [
            'App\Listeners\CreatePurchasePoints',
            'App\Listeners\CreateScratchCards',
            'App\Listeners\VoucherNotification',
            'App\Listeners\CreateSpinners',
        ],
        'App\Events\CustomerRegister' => [
            'App\Listeners\CustomerRegisterEmailNotification',
            'App\Listeners\CustomerRegisterSMSNotification',
        ],
        'App\Events\ScratchCardWinner' => [
            'App\Listeners\ScratchCardWinnerEmailNotification',
            'App\Listeners\ScratchCardWinnerSMSNotification',
        ],
        'App\Events\RewardPointVoucher' => [
            'App\Listeners\RewardPointEmailNotification',
            'App\Listeners\RewardPointSMSNotification',
        ],
        'App\Events\RaffleDraw' => [
            'App\Listeners\RaffleDrawEmailNotification',
            'App\Listeners\RaffleDrawSMSNotification',
        ],
        'App\Events\SpunWinner' => [
            'App\Listeners\SpunWinnerEmailNotification',
            'App\Listeners\SpunWinnerSMSNotification',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
