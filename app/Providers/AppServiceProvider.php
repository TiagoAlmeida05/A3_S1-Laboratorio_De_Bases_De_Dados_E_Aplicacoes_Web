<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\JobPosting::class => \App\Policies\JobPostingPolicy::class
    ]; // DO LATER: add policies!!
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ??
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void {
        Paginator::useBootstrapFive();

        // $this->registerPolicies();

        Gate::define('create-job-posting', function ($user) {
            return $user->recruiter !== null;
        });
    }
}
