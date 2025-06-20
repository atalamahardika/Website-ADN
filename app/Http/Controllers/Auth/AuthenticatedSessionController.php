<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        // ✅ Cek dan buat relasi 'member' jika belum ada
        if ($user->role === 'member' && !$user->member) {
            $user->member()->create(); // buat data member kosong
        }

        $url = 'dashboard';

        if ($request->user()->role === 'admin') {
            $url = 'admin/dashboard';
            return redirect()->intended($url);
        } elseif ($request->user()->role === 'super admin') {
            $url = 'superadmin/dashboard';
            return redirect()->intended($url);
        }

        return redirect()->intended($url);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
