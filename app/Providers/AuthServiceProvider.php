<?php

namespace App\Providers;

use App\Models\Company;
use App\Policies\CompanyPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{

    protected $policies = [
        Company::class => CompanyPolicy::class,
        JobSeeker::class => JobSeekerPolicy::class,
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}