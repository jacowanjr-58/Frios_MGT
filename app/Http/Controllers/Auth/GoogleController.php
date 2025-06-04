<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    // Redirect to Google login
    public function redirectToGoogle()
    {
       
        return Socialite::driver('google')->redirect();
    }

    // Handle Google Callback
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // Check if the user exists
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                // Log in the user
                Auth::login($user);
                return redirect()->route('dashboard'); // Change 'dashboard' to your home page route
            } else {
                return redirect()->route('login')->with('error', 'No account found with this Google email.');
            }
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Something went wrong. Please try again.');
        }
    }
}
