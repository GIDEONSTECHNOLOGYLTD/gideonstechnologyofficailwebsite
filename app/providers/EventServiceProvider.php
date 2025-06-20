<?php

namespace App\Providers;

use App\Core\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\UserRegistered' => [
            'App\Listeners\SendWelcomeEmail',
        ],
        'App\Events\OrderPlaced' => [
            'App\Listeners\SendOrderConfirmation',
            'App\Listeners\UpdateInventory',
        ],
        'App\Events\PaymentReceived' => [
            'App\Listeners\SendPaymentReceipt',
            'App\Listeners\UpdateOrderStatus',
        ],
        'App\Events\ServiceRequested' => [
            'App\Listeners\NotifyAdministrator',
            'App\Listeners\ScheduleService',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        // Register event listeners from the $listen array
        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                if (class_exists($event) && class_exists($listener)) {
                    $this->app->get('events')->listen($event, $listener);
                }
            }
        }
        
        // Register any other events...
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register the event dispatcher service if needed
        $this->app->set('events', function ($c) {
            return new \App\Core\Events\Dispatcher($c);
        });
    }
}
