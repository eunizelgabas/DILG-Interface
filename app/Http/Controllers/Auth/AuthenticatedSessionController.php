<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
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
        // $request->authenticate();
        // $request->session()->regenerate();

        $credentials = $request->only('email', 'password');

        // Attempt to authenticate the user
        if (auth()->attempt($credentials)) {
            $user = auth()->user();

            // Check if the user has the 'standard' role
            if ($user->hasRole('Standard') || $user->status == 0) {
                auth()->logout(); // Logout the user
                return redirect()->route('login')->with('error', 'Unauthorized. You do not have the necessary role.');
                // Alternatively, redirect to a different route or show a more specific error message.
            }

            // If the user does not have the 'standard' role or has status 0, proceed with the login
            $request->session()->regenerate();
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        // Authentication failed
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
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
