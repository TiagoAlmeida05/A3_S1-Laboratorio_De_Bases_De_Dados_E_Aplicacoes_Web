<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
use App\Models\Message;

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

        View::composer('*', function ($view) {
            if(Auth::check()){
                $userId = Auth::id();

                $bellCount = DB::table('notification')
                    ->where('registered_user_id', $userId)
                    ->whereNull('read_date')
                    ->where('notification_type_id', '!=', 2)
                    ->count();

                $letterCount = DB::table('message')
                    ->where('receiver_id', $userId)
                    ->whereNull('date_read')
                    ->count();

                $view->with('bellCount', $bellCount);
                $view->with('letterCount', $letterCount);
            }
        });
    }
}
