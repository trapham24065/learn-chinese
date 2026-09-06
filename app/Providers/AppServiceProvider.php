<?php

namespace App\Providers;

use App\Models\StudySession;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;
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

        ResetPassword::toMailUsing(function ($notifiable, $token) {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            return (new MailMessage)
                ->subject('[Learn Chinese] Hướng dẫn đặt lại mật khẩu tài khoản')
                ->greeting('Xin chào ' . ($notifiable->name ?? 'bạn') . '!')
                ->line('Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản học tiếng Trung của bạn tại Learn Chinese.')
                ->action('Đặt lại mật khẩu', $url)
                ->line('Liên kết này sẽ hết hạn sau ' . config('auth.passwords.users.expire', 60) . ' phút.')
                ->line('Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email. Mật khẩu của bạn vẫn được giữ an toàn.')
                ->salutation("Trân trọng,\nĐội ngũ phát triển Learn Chinese");
        });

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
