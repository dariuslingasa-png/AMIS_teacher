<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return redirect()->route('admin.login');
        }

        if (! Auth::user()->hasAdminPortalAccess()) {
            abort(403);
        }

        if ((Auth::user()->account_status ?? 'verified') !== 'verified') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'Account access is disabled. Please contact the system administrator.']);
        }

        if (
            Auth::user()->active_admin_session_id
            && Auth::user()->active_admin_session_id !== $request->session()->getId()
        ) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'This account was signed in from another device. Please log in again.']);
        }

        return $next($request);
    }
}
