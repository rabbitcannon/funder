<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Gate;
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
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        // for most EOS services, all we are doing is whitelisting a service; so it's
        // fine to have non-expiring tokens. If we need to expire tokens, adjust this here:
        // Passport::tokensExpireIn(Carbon::now()->addDays(1)); // 1 day expiration
        // Passport::refreshTokensExpireIn(Carbon::now()->addDays(30));
    }
}
