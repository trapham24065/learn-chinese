<?php

namespace App\Providers;

use App\Models\StudySession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $user = Auth::guard('web')->user();
            $streak = $user ? $user->calculateStreak() : 0;

            $view->with([
                'authUser' => $user,
                'sidebarStreak' => $streak,
            ]);
        });
    }
}
