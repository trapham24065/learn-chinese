<?php

namespace App\Providers;

use App\Models\StudySession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            \Filament\Auth\Http\Responses\Contracts\LoginResponse::class,
            \App\Http\Responses\FilamentLoginResponse::class,
        );

        $this->app->bind(
            \Filament\Auth\Http\Responses\Contracts\LogoutResponse::class,
            \App\Http\Responses\FilamentLogoutResponse::class,
        );
    }

    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        View::composer('layouts.app', function ($view) {
            $user = Auth::guard('web')->user();

            // C2: Cache per-user streak for 1 hour to avoid loading all study_sessions on every page
            $streak = $user
                ? Cache::remember("streak_{$user->id}", 3600, fn () => $user->calculateStreak())
                : 0;

            $view->with([
                'authUser'      => $user,
                'sidebarStreak' => $streak,
            ]);
        });
    }
}
