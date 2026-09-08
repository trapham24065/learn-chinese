<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        // If maintenance mode is off, proceed
        if (! function_exists('setting_bool') || ! setting_bool('maintenance_mode', false)) {
            return $next($request);
        }

        // Bypass for admin panel routes and filament routes
        if ($request->is('admin*') || $request->is('livewire*')) {
            return $next($request);
        }

        // Bypass if user is logged in as admin
        if ($request->user() && $request->user()->isAdmin()) {
            return $next($request);
        }

        // Show maintenance page
        $message = function_exists('setting')
            ? setting('maintenance_message', 'Website đang được nâng cấp. Vui lòng quay lại sau ít phút!')
            : 'Website đang được nâng cấp. Vui lòng quay lại sau ít phút!';

        return response()->view('errors.maintenance', [
            'message' => $message,
        ], 503);
    }
}
