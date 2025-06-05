<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Account;
use Stripe\AccountLink;
use Stripe\PaymentIntent;

class StripeController extends Controller
{
    public function createConnectedAccount()
    {
       
        Stripe::setApiKey( config('stripe.secret_key'));
       
        $account = Account::create([
            'type' => 'express',
        ]);
      
        auth()->user()->update([
            'stripe_account_id' => $account->id,
        ]);

        $link = AccountLink::create([
            'account' => $account->id,
            'refresh_url' => route('franchise.stripe.refresh'),
            'return_url' => route('franchise.stripe.return'),
            'type' => 'account_onboarding',
        ]);

        return redirect($link->url);
    }

    public function refreshOnboarding()
    {
        return redirect()->route('franchise.stripe.onboard');
    }

    public function returnOnboarding()
    {
        return redirect()->route('franchise.stripe')
            ->with('success', 'Stripe connected successfully');

    }

    public function showPayForm(User $recipient)
    {
        return view('pay', ['recipient' => $recipient]);
    }

    public function createPaymentIntent(Request $request, User $recipient)
    {
        Stripe::setApiKey( config('stripe.secret_key'));

        $amount = 5000; // e.g., $50 in cents

        $paymentIntent = PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'usd',
            'payment_method_types' => ['card'],
            'transfer_data' => [
                'destination' => $recipient->stripe_account_id,
            ],
        ]);

        return response()->json([
            'clientSecret' => $paymentIntent->client_secret,
        ]);
    }
}
