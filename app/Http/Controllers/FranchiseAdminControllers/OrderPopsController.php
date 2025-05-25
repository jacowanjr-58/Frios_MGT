<?php

namespace App\Http\Controllers\FranchiseAdminControllers;

use Carbon\Carbon;
use App\Models\FgpItem;
use App\Models\FgpOrder;
use App\Models\FgpCategory;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\AdditionalCharge;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Stripe\Stripe;
use Stripe\Charge;
use App\Mail\OrderPaidMail;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\OrderTransaction;
use Stripe\Checkout\Session as StripeSession;
use App\Models\User;
use DB;

class OrderPopsController extends Controller
{
    public function index()
    {
        $currentMonth = strval(Carbon::now()->format('n')); // Get current month as single-digit (1-12)

        // Fetch only items that are orderable, in stock, and available in the current month
        $pops = FgpItem::where('orderable', 1)
            ->where('internal_inventory', '>', 0) // Ensure the item is in stock
            ->get()
            ->filter(function ($pop) use ($currentMonth) {
                $availableMonths = json_decode($pop->dates_available, true);
                return in_array($currentMonth, $availableMonths ?? []);
            });

        // Count total available flavor pops
        $totalPops = $pops->count();

        return view('franchise_admin.orderpops.index', compact('pops', 'totalPops'));
    }

    public function create()
    {
        $currentMonth = strval(Carbon::now()->format('n'));

        // Fetch only orderable, in-stock, and currently available items
        $pops = FgpItem::where('orderable', 1)
            ->where('internal_inventory', '>', 0) // Ensure item is in stock
            ->get()
            ->filter(function ($pop) use ($currentMonth) {
                $availableMonths = json_decode($pop->dates_available, true);
                return in_array($currentMonth, $availableMonths ?? []);
            });

        $categorizedItems = [];
        foreach ($pops as $pop) {
            foreach ($pop->categories as $category) {
                $types = json_decode($category->type, true); // Decode JSON types

                foreach ($types as $type) {
                    $categorizedItems[$type][$category->name][] = $pop;
                }
            }
        }

        return view('franchise_admin.orderpops.create', compact('categorizedItems'));
    }

    // public function confirmOrder(Request $request)
    // {
    //     \Log::info('Received Order Data:', ['ordered_items' => $request->input('ordered_items')]);

    //     // Decode the JSON input
    //     $items = json_decode($request->input('ordered_items'), true) ?: [];

    //     if (empty($items)) {
    //         \Log::warning('No items received in order confirmation.');
    //     }

    //     // Fetch additional charges from the database
    //     $requiredCharges = AdditionalCharge::where('charge_optional', 'required')->get();
    //     $optionalCharges = AdditionalCharge::where('charge_optional', 'optional')->get();

    //     return view('franchise.orderpops.confirm', compact('items', 'requiredCharges', 'optionalCharges'));
    // }

    public function confirmOrder(Request $request)
{
    try {
        $items = $request->input('ordered_items');

        \Log::info('Received Order Data:', ['ordered_items' => $items]);

        if (empty($items)) {
            \Log::warning('No items received in order confirmation.');
            return response()->json(['error' => 'No items selected for order.'], 400);
        }

        // Convert price strings to numeric values for calculations
        foreach ($items as &$item) {
            $item['price'] = floatval(str_replace(['$', ','], '', $item['price']));
            $item['quantity'] = $item['quantity'] ?? 1; // Set default quantity if not provided
        }

        // Store items in the session for retrieval on the confirmation page
        session(['ordered_items' => $items]);

        return response()->json(['redirect' => route('franchise.orderpops.confirm.page')]);
    } catch (\Exception $e) {
        \Log::error('Error in confirmOrder method: ' . $e->getMessage());
        return response()->json(['error' => 'Something went wrong. Please try again.'], 500);
    }
}

public function showConfirmPage()
{
    $items = session('ordered_items', []);

    if (empty($items)) {
        return redirect()->route('franchise.orderpops.index')->withErrors('No items selected.');
    }

    $requiredCharges = AdditionalCharge::where('charge_optional', 'required')->where('status', 1)->get();
    $optionalCharges = AdditionalCharge::where('charge_optional', 'optional')->where('status', 1)->get();

    return view('franchise_admin.orderpops.confirm', compact('items', 'requiredCharges', 'optionalCharges'));
}


public function store(Request $request)
{
    $minCases = 12;
    $factorCase = 3;

    $validated = $request->validate([
        'grandTotal' => 'required|numeric|min:1',
        'items' => 'required|array',
        'items.*.fgp_item_id' => 'required|exists:fgp_items,fgp_item_id',
        'items.*.user_ID' => 'required|exists:users,user_id',
        'items.*.unit_cost' => 'required|numeric|min:0',
        'items.*.unit_number' => 'required|integer|min:1',
    ]);

    $totalCaseQty = collect($validated['items'])->sum('unit_number');

    if ($totalCaseQty < $minCases) {
        return redirect()->back()->withErrors(['Order must have at least ' . $minCases . ' cases.']);
    }

    if ($totalCaseQty % $factorCase !== 0) {
        return redirect()->back()->withErrors(['Order quantity must be a multiple of ' . $factorCase . '.']);
    }

    $order = \App\Models\FgpOrder::create([
        'user_ID' => Auth::user()->franchisee_id,
        'date_transaction' => now(),
        'status' => 'Pending',
    ]);

    foreach ($validated['items'] as $item) {
        DB::table('fgp_order_details')->insert([
            'fgp_order_id' => $order->fgp_ordersID,
            'fgp_item_id' => $item['fgp_item_id'],
            'unit_cost' => $item['unit_cost'],
            'unit_number' => $item['unit_number'],
            'date_transaction' => now(),
            'ACH_data' => null,
        ]);
    }

    // Stripe Checkout Session Creation
    \Stripe\Stripe::setApiKey(apiKey: config('stripe.secret_key'));

    try {
        $amountInCents = $request->grandTotal * 100;

        $checkoutSession = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Order Payment - ' . Auth::user()->name,
                    ],
                    'unit_amount' => $amountInCents,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('payment.successs') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('payment.cancell'),
            'metadata' => [
                'order_id' => $order->fgp_ordersID,
                'franchisee_id' => Auth::user()->franchisee_id,
            ],
        ]);

        $paymentUrl = $checkoutSession->url;

    } catch (\Exception $e) {
        return redirect()->back()->withErrors(['Stripe Error: ' . $e->getMessage()]);
    }

    // Generate PDF
    $orderDetails = \App\Models\FgpOrderDetail::where('fgp_order_id', $order->fgp_ordersID)->get();
    $franchisee = \App\Models\Franchisee::where('franchisee_id', $order->user_ID)->firstOrFail();

    $pdf = \PDF::loadView('franchise_admin.payment.pdf.order-pos', compact('order', 'franchisee', 'orderDetails'));
    $pdfPath = storage_path('app/public/order_invoice_' . $order->id . '.pdf');
    $pdf->save($pdfPath);

    // Send Email
    $franchiseeadmin = \App\Models\User::where(['franchisee_id' => Auth()->user()->franchisee_id , 'role' => 'franchise_admin'])->first();
    if ($franchiseeadmin) {
        \Mail::to($franchiseeadmin->email)->send(
            new \App\Mail\OrderPaidMail($franchiseeadmin, $order, $pdfPath, $paymentUrl)
        );
    }

    unlink($pdfPath); // Remove the PDF after sending

    return redirect()->route('franchise.orderpops.view')->with('success', 'Order placed successfully!');
}




public function viewOrders()
{
    // $orders = FgpOrder::where('user_ID', Auth::id())
    //     ->select(
    //         'user_ID',
    //         'date_transaction',
    //         \DB::raw('SUM(unit_number) as total_quantity'),
    //         \DB::raw('SUM(unit_number * unit_cost) as total_amount'),
    //         'status'
    //     )
    //     ->groupBy('date_transaction', 'user_ID', 'status')
    //     ->orderBy('date_transaction', 'desc')
    //     ->with('user')
    //     ->get()
    //     ->map(function ($order) {
    //         $order->date_transaction = Carbon::parse($order->date_transaction);
    //         return $order;
    //     });

    $orders = FgpOrder::where('user_ID' , Auth::user()->franchisee_id)->get();

    $totalOrders = $orders->count();

    return view('franchise_admin.orderpops.vieworders', compact('orders', 'totalOrders'));
}


public function customer($franchisee_id)
{
    $customers = Customer::where('franchisee_id', $franchisee_id)->get();

    return response()->json([
        'data' => $customers,
    ]);
}


public function success(Request $request){
        $sessionId = $request->get('session_id');

    if (!$sessionId) {
        return 'Missing Stripe session ID.';
    }

    \Stripe\Stripe::setApiKey(config('stripe.secret_key'));

    try {
        // Get the Checkout Session
        $session = StripeSession::retrieve($sessionId);

        // Get PaymentIntent if available
        $paymentIntent = \Stripe\PaymentIntent::retrieve($session->payment_intent);

        // Get metadata
        $orderId = $session->metadata->order_id ?? null;
        $franchiseeId = $session->metadata->franchisee_id ?? null;

        if (!$orderId || !$franchiseeId) {
            return 'Missing metadata from Stripe.';
        }

        // Check if transaction already exists (avoid duplicates)
        $existing = OrderTransaction::where('stripe_payment_intent_id', $session->payment_intent)->first();
        if ($existing) {
            return 'Payment already recorded.';
        }

        // Create the transaction
        OrderTransaction::create([
            'franchisee_id' => $franchiseeId,
            'fgp_order_id' => $orderId,
            'cardholder_name' => $session->customer_details->name ?? 'Unknown',
            'amount' => $session->amount_total / 100,
            'stripe_payment_intent_id' => $session->payment_intent,
            'stripe_payment_method' => $paymentIntent->payment_method ?? null,
            'stripe_currency' => $session->currency,
            'stripe_client_secret' => $paymentIntent->client_secret ?? null,
            'stripe_status' => $session->payment_status,
        ]);

        // Optionally: update order status
        $order = FgpOrder::find($orderId);
        if ($order) {
            $order->status = 'Paid';
            $order->save();
        }

            return view('thankyou');

    } catch (\Exception $e) {
        return 'Stripe error: ' . $e->getMessage();
    }
}
}

