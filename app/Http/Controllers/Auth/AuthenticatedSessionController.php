<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Franchisee;
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

        $user = auth()->user();
        $franchisees = $user->franchisees;
      
        // Check the role and redirect accordingly, with a success message
        if ($user->hasRole('corporate_admin')) {
            return redirect()->intended(route('dashboard', absolute: false))
                ->with('success', 'Welcome Back, ' . $user->name);
        } elseif ($user->hasRole('franchise_admin')) {
           
            
           
            if ($franchisees->count() > 1) {
                return redirect()->route('franchise.select_franchisee');
            } elseif ($franchisees->count() === 1) {
                return redirect("/franchise/{$franchisees->first()->franchisee_id}/dashboard")->with('success', 'Welcome Back, ' . $user->name);
            }
        }  else {
            return  redirect("/franchise/{$franchisees->first()->franchisee_id}/dashboard")
                ->with('success', 'Welcome Back, ' . $user->name);
        }

        return redirect()->intended(route('dashboard', absolute: false))->with('success', 'Welcome Back');
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
