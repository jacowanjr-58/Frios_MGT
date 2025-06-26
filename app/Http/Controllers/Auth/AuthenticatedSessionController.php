<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Franchise;

use App\Models\User;
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
        $user = Auth::user();

        // Get user's franchises (corrected relationship name)
        $userFranchises = $user->franchises;
        // Get the first franchise ID for redirection
        $firstFranchiseId = $userFranchises->isNotEmpty()
        ? $userFranchises->first()->id
        : Franchise::first()?->id;

        session(['franchise_id' => $firstFranchiseId]);


        if ($user->role == 'super_admin') {
            return redirect()->intended(route('dashboard', absolute: false))
                ->with('success', 'Welcome Back, ' . $user->name);
        }

        // Check the role and redirect accordingly, with a success message
        if ($user->role == 'corporate_admin') {
            return redirect("/franchise/{$firstFranchiseId}/dashboard")
                ->with('success', 'Welcome Back, ' . $user->name);
        } elseif ($user->role == 'franchise_admin') {
            if ($userFranchises->count() > 1) {
                return redirect()->route('franchise.select_franchise');
            } elseif ($userFranchises->count() === 1) {
                return redirect("/franchise/{$userFranchises->first()->id}/dashboard")->with('success', 'Welcome Back, ' . $user->name);
            }
        } else {
            return redirect("/franchise/{$firstFranchiseId}/dashboard")
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
