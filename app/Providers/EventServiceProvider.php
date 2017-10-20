<?php
/**
 * \class EventServiceProvider
 *
 * Default Event Service provider modified with additional event calls
 */
namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * \brief Default Event Service provider modified with additional event calls
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     * @var array
     */
    protected $listen = [
		'Illuminate\Auth\Events\Login'=>['App\Listeners\LogSuccessfulLogin'],
		'Illuminate\Auth\Events\Failed'=>['App\Listeners\LogFailedLogin'],
		'Illuminate\Auth\Events\Attempting'=>['App\Listeners\LogLoginAttempt'],
		'Illuminate\Auth\Events\Registered'=>['App\Listeners\LogRegisteredUser'],
    ];




    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
	    public function boot(DispatcherContract $events)
     */
    public function boot()
    {
        parent::boot();
    }
}
