<?php
/**
 * \class	AuthServiceProvider
 * \version	1.0.1
 *
 *
 *
 * {INFO_2018-10-03} Added support for Gate()
 */
namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

		Gate::define('admin-only', function($user)
		{
			if($user->role_id == 1)
			{
				return true;
			}
			return false;
		});
    }
}
