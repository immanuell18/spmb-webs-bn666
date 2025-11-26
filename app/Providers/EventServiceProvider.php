<?php

namespace App\Providers;

use App\Events\UserRegistered;
use App\Events\DocumentVerificationRequested;
use App\Events\PaymentInstructionSent;
use App\Events\PaymentVerified;
use App\Events\SelectionResultAnnounced;
use App\Listeners\SendNotificationListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserRegistered::class => [
            SendNotificationListener::class,
        ],
        DocumentVerificationRequested::class => [
            SendNotificationListener::class,
        ],
        PaymentInstructionSent::class => [
            SendNotificationListener::class,
        ],
        PaymentVerified::class => [
            SendNotificationListener::class,
        ],
        SelectionResultAnnounced::class => [
            SendNotificationListener::class,
        ],
    ];

    public function boot(): void
    {
        //
    }

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}