<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Stripe as StripeModel;
class StripeMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
       
        if (Auth::user()->role == "franchise_admin") {


            // $franchiseeId = Auth::user()->franchisee_id;
            $user = Auth::user();
          
            // $franchiseeId =  $user->load('franchisees');
            // $user = User::where('franchisee_id', $franchiseeId)->first();
            if ($user->stripe_account_id == null) {
                return redirect()->route('franchise.stripe')->with('error', 'You have not connect a Stripe account yet. Please connect one to proceed.');
            } else {
            
                return $next($request);
            }

        } else {
            return $next($request);
        }
    }
}
