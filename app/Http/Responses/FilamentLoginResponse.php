<?php

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as Responsable;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class FilamentLoginResponse implements Responsable
{
    public function toResponse($request): RedirectResponse | Redirector
    {
        Notification::make()
            ->title('Đăng nhập thành công!')
            ->body('Chào mừng trở lại trang quản trị.')
            ->success()
            ->send();

        return redirect()->intended(Filament::getUrl());
    }
}
