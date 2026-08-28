<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\AdminDashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use App\Filament\Widgets\LearningOverview;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->font('Inter', provider: \Filament\FontProviders\LocalFontProvider::class)
            ->path('admin')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('5rem')
            ->favicon(asset('images/favicon.png'))
            ->brandName('Learn Chinese')
            ->authGuard('admin')
            ->login()
            ->homeUrl(fn(): string => AdminDashboard::getUrl())
            ->colors([
                'primary' => Color::Amber,
            ])
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render('@if(session()->has("success"))
                    <div id="filament-toast" style="position:fixed;top:20px;right:20px;z-index:99999;display:flex;align-items:center;gap:12px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:14px 20px;box-shadow:0 10px 25px rgba(0,0,0,.12);min-width:300px;opacity:1;transition:all .5s ease;">
                        <div style="flex-shrink:0;width:36px;height:36px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:14px;font-weight:700;color:#111827;">Thành công</div>
                            <div style="font-size:12px;font-weight:500;color:#6b7280;margin-top:2px;">{{ session("success") }}</div>
                        </div>
                        <button onclick="this.parentElement.remove()" style="color:#9ca3af;cursor:pointer;background:none;border:none;font-size:18px;line-height:1;padding:4px;">&times;</button>
                    </div>
                    <script>setTimeout(()=>{const t=document.getElementById("filament-toast");if(t){t.style.opacity="0";t.style.transform="translateY(20px)";setTimeout(()=>t.remove(),500);}},4000);</script>
                @endif'),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                AdminDashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
