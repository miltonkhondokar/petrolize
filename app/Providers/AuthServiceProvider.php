<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->registerPolicies();

        // ✅ REQUIRED for password grant in newer Passport versions
        Passport::enablePasswordGrant();

        Passport::routes();
    }
}
