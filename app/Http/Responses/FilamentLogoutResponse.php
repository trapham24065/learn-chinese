<?php

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LogoutResponse as Responsable;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class FilamentLogoutResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        $loginUrl = Filament::hasLogin() ? Filament::getLoginUrl() : Filament::getUrl();

        return redirect()->to($loginUrl)->with('success', 'Bạn đã đăng xuất an toàn. Hẹn gặp lại!');
    }
}
