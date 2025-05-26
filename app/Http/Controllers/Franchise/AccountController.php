<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Account;
use Stripe\Stripe;
use Stripe\PaymentMethod;
use Stripe\Customer;
use Stripe\Exception\ApiErrorException;

class AccountController extends Controller
{
    public function index()
    {
        $accounts = Account::where('franchisee_id', auth()->user()->franchisee_id)->get();
        $accountCount = Account::where('franchisee_id', auth()->user()->franchisee_id)->count();
        return view('franchise_admin.accounts.index', compact('accounts' , 'accountCount'));
    }

        public function create()
    {
        return view('franchise_admin.accounts.create');
    }

public function store(Request $request)
{
    $stripeToken = $request->input('stripe_token');
    $cardholderName = $request->input('cardholder_name');
    $isActive = $request->input('is_active');

    \Log::info("Received Stripe Token: " . $stripeToken);

    \Stripe\Stripe::setApiKey(apiKey: config('stripe.secret_key'));

    try {
        $charge = \Stripe\Charge::create([
            'amount' => 5000,
            'currency' => 'usd',
            'source' => $stripeToken,
            'description' => "Payment for $cardholderName",
        ]);

        $account = Account::create([
            'franchisee_id' => auth()->user()->franchisee_id,
            'cardholder_name' => $cardholderName,
            'stripeToken' => $stripeToken,
            'stripe_customer_id' => $charge->customer,
            'stripe_payment_method_id' => $charge->payment_method,
            'is_active' => $isActive,
        ]);

        return response()->json(['message' => 'Payment successful!'], 200);
    } catch (\Stripe\Exception\CardException $e) {
        return response()->json(['error' => 'Payment failed: ' . $e->getMessage()], 400);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        return response()->json(['error' => 'Payment failed: ' . $e->getMessage()], 500);
    }
}




    public function edit(Account $account)
    {
        return view('franchise_admin.accounts.edit', compact('account'));
    }

public function update(Request $request, Account $account)
{
    // Remove spaces in card number
    $request->merge([
        'card_number' => str_replace(' ', '', $request->input('card_number')),
    ]);

    // Validate incoming request
    $validated = $request->validate([
        'cardholder_name' => 'required|string|max:255',
        'card_number' => 'required|digits_between:13,19',
        'card_expiry' => ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
        'card_cvc' => 'required|digits_between:3,4',
        'is_active' => 'nullable|boolean',
    ]);

    // If this card is marked as active, deactivate all other cards for the same franchisee
    if ($request->input('is_active')) {
        // Deactivate all other cards for this franchisee
        Account::where('franchisee_id', $account->franchisee_id)
            ->where('id', '!=', $account->id) // Exclude the current card
            ->update(['is_active' => 0]);
    }

    // Update the card with new data
    $account->update([
        'cardholder_name' => $request->input('cardholder_name'),
        'card_number' => $request->input('card_number'),
        'card_expiry' => $request->input('card_expiry'),
        'card_cvc' => $request->input('card_cvc'),
        'is_active' => $request->boolean('is_active'),
    ]);

    // Redirect back to the accounts page
    return redirect()->route('franchise.account.index')->with('success', 'Account updated successfully.');
}


    public function destroy(Account $account)
    {
        $account->delete();
        return redirect()->route('franchise.account.index')->with('success', 'Account deleted successfully.');
    }
}
