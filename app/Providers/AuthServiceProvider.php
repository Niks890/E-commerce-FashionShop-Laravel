<?php

namespace App\Providers;

use App\Models\Customer;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // $this->registerPolicies();
        // Gate::define('customers', function () {
        //     return Auth::guard('customer')->check();
        // });


        Gate::define('managers', function(User $user) {
            return in_array('admin', $user->roles()) || in_array('manage', $user->roles()) ? true : false;
        });

        Gate::define('salers', function(User $user) {
            return in_array('admin', $user->roles()) || in_array('manage', $user->roles()) || in_array('sale', $user->roles()) ? true : false;
        });

        Gate::define('warehouse workers', function(User $user) {
            return in_array('admin', $user->roles()) || in_array('manage', $user->roles()) || in_array('inventory', $user->roles()) ? true : false;
        });

         Gate::define('delivery workers', function(User $user) {
            return  in_array('delivery', $user->roles()) ? true : false;
        });
    }
}
