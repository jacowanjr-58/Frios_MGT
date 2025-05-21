<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;
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

            $franchiseeId = Auth::user()->franchisee_id;

            $stripe = StripeModel::where('franchisee_id', $franchiseeId)->first();

            if ($stripe == null) {
                return redirect()->route('franchise.stripe')->with('error', 'You have not created a Stripe account yet. Please create one to proceed.');
            } else {
                return $next($request);
            }

        } else {
            return $next($request);
        }
    }

}
