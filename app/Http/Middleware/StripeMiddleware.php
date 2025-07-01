<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class StripeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::user()->hasRole('franchise_admin')) {

            // $franchiseeId = Auth::user()->franchise_id;
            $user = Auth::user();
           
            // $franchiseeId =  $user->load('franchisees');
            // $user = User::where('franchise_id', $franchiseeId)->first();
            if (!$user->stripe_account_id) {
                return redirect()->route('franchise.stripe.onboard')->with('error', 'You have not connect a Stripe account yet. Please connect one to proceed.');
            }

        }

        return $next($request);
    }
}
