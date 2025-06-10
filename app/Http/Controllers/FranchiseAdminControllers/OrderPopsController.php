<?php

namespace App\Http\Controllers\FranchiseAdminControllers;

use Carbon\Carbon;
use App\Models\FgpItem;
use App\Models\FgpOrder;
use App\Models\FgpCategory;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Franchisee;
use App\Models\User;
use App\Services\ShipStationService;
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
use Illuminate\Support\Facades\DB;

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
        $currentMonth = (string) Carbon::now()->month;

    $items = FgpItem::with('categories')
        ->where('orderable', 1)
        ->where('internal_inventory', '>', 0)
        ->get()
        ->filter(function ($item) use ($currentMonth) {
            $months = json_decode($item->dates_available, true);
            return in_array($currentMonth, $months ?? []);
        });

        $categoriesByType = FgpCategory::select('category_ID', 'name', 'type')
        ->with(['items' => function ($q) {
            $q->select(
                    'fgp_Items.fgp_item_id',
                    'fgp_Items.name',
                    'fgp_Items.image1',
                    'fgp_Items.case_cost'
                )
                ->where('fgp_Items.orderable', 1)
                ->where('fgp_Items.internal_inventory', '>', 0)
                ->whereJsonContains('fgp_Items.dates_available', (string) Carbon::now()->month)
                ->orderBy('fgp_Items.name');
        }])
        // optional: if you want your types in a specific order
        ->orderBy('type')
        ->get()
        ->groupBy('type');







    return view('franchise_admin.orderpops.create', compact('categoriesByType'));
}


    public function confirmOrder(Request $request)
    {


           $items = json_decode($request->input('ordered_items'), true);



            if (empty($items)) {

                return response()->json(['error' => 'No items selected for order.'], 400);
            }

            // Convert price strings to numeric values for calculations
            foreach ($items as &$item) {
                $item['case_cost'] = floatval(str_replace(['$', ','], '', $item['case_cost']));
                $item['quantity'] = $item['quantity'] ?? 1; // Set default quantity if not provided
            }
        //dd($items);
            // Store items in the session for retrieval on the confirmation page
            session(['ordered_items' => $items]);
           // return view('franchise_admin.orderpops.confirm', compact('items'));
           return redirect()->route('franchise.orderpops.confirm.page');

}

public function showConfirmPage()
{
    $items = session('ordered_items', []);

    if (empty($items)) {
        return redirect()->route('franchise.orderpops.index')->withErrors('No items selected.');
    }
        $franchiseeId = Auth::user()->franchisee_id;

        $customers = Customer::where('franchisee_id', $franchiseeId)->get();

        $franchisee = Franchisee::where('franchisee_id', $franchiseeId)->get();

    $requiredCharges = AdditionalCharge::where('charge_optional', 'required')->where('status', 1)->get();
    $optionalCharges = AdditionalCharge::where('charge_optional', 'optional')->where('status', 1)->get();

 return view('franchise_admin.orderpops.confirm', compact('items', 'requiredCharges', 'optionalCharges', 'customers', 'franchisee'));
}


public function store(Request $request)
{
    $minCases = 12;
    $factorCase = 3;

    $validated = $request->validate([
        'grandTotal' => 'required|numeric|min:1',
        'items' => 'required|array',
        'items.*.fgp_item_id' => 'required|exists:fgp_items,fgp_item_id',
        //'items.*.user_ID' => 'required|exists:users,user_id',
        'items.*.unit_cost' => 'required|numeric|min:0',
        'items.*.unit_number' => 'required|integer|min:1',
        'ship_to_name' => 'required|string',
            'ship_to_address1' => 'required|string',
            'ship_to_city' => 'required|string',
            'ship_to_state' => 'required|string',
            'ship_to_zip' => 'required|string',
            'ship_to_phone' => 'nullable|string',
            'ship_to_country' => 'nullable|string',
            'ship_method' => 'nullable|string'
    ]);

      if ($request->is_paid === '1') {
            $rules['stripeToken'] = 'required|string';
            $rules['cardholder_name'] = 'required|string|max:191';
        }



        // Enforce minimum and multiple case logic
        $totalCaseQty = collect($validated['items'])->sum('unit_number');
        $minCases = 12;
        $factorCase = 3;


    if ($totalCaseQty < $minCases) {
        return redirect()->back()->withErrors(['Order must have at least ' . $minCases . ' cases.']);
    }

    if ($totalCaseQty % $factorCase !== 0) {
        return redirect()->back()->withErrors(['Order quantity must be a multiple of ' . $factorCase . '.']);
    }

     // Stripe payment processing
        $charge = null;
        if ($request->is_paid === '1') {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

            try {
                $charge = \Stripe\Charge::create([
                    'amount' => $request->grandTotal * 100,
                    'currency' => 'usd',
                    'description' => 'Order Payment by: ' . $request->cardholder_name,
                    'source' => $request->stripeToken,
                    'metadata' => [
                        'franchisee_id' => Auth::user()->franchisee_id,
                    ],
                ]);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['Stripe Error: ' . $e->getMessage()]);
            }
        }

// Create the order
    $order = FgpOrder::create([
        'user_ID' => Auth::user()->user_id,
        'franchisee_id' => Auth::user()->franchisee_id,
        'date_transaction' => now(),
        'status' => 'Pending',
        'is_paid' => $request->is_paid === '1',
        'ship_to_name' => $request->ship_to_name,
        'ship_to_address1' => $request->ship_to_address1,
        'ship_to_address2' => $request->ship_to_address2,
        'ship_to_city' => $request->ship_to_city,
        'ship_to_state' => $request->ship_to_state,
        'ship_to_zip' => $request->ship_to_zip,
        'ship_to_country' => $request->ship_to_country ?? 'US',
        'ship_to_phone' => $request->ship_to_phone,
        'ship_method' => $request->ship_method ?? 'Standard',
        'shipstation_status' => 'awaiting_shipment',
    ]);

        $orderNum = 'FGP-' . $order->fgp_ordersID; // Unique, readable, traceable

 // Store order items
   $orderItems = [];
   $noteLines[] = "Ordered Items:";

foreach ($validated['items'] as $index => $item) {
    DB::table('fgp_order_details')->insert([
        'fgp_order_id' => $order->fgp_ordersID,
        'fgp_item_id' => $item['fgp_item_id'],
        'unit_cost' => $item['unit_cost'],
        'unit_number' => $item['unit_number'],
        'date_transaction' => now(),
        'ACH_data' => null,
    ]);

    // Optional: get item name/sku from DB or cache beforehand if not in $item
    $fgpItem = \App\Models\FgpItem::find($item['fgp_item_id']);

    $orderItems[] = [
      //  'lineItemKey' => 'line_' . $index, // Can be more unique if needed
       'sku' => $fgpItem->name ?? 'UNKNOWN',
        'name' => $fgpItem->name ?? 'Item',
        'quantity' => (int) $item['unit_number'],
        'unitPrice' => (float) $item['unit_cost'],
    ];

    $name = $fgpItem->name ?? 'Unnamed';
    $qty = $item['unit_number'];
    $cost = number_format($item['unit_cost'], 2);
    $total = number_format($item['unit_number'] * $item['unit_cost'], 2);

    $noteLines[] = "- {$name} | Qty: {$qty} | Cost: \${$cost} | Total: \${$total}";

}
    $invoiceNotes = implode("\n", $noteLines);

    // Save transaction or invoice area
    // Need to update this base on ultimate payment method
    // If paid, save transaction; if not, save invoice
    if ($request->is_paid === '1') {
        OrderTransaction::create([
            'franchisee_id' => Auth::user()->franchisee_id,
            'fgp_order_id' => $order->fgp_ordersID,
            'order_num' => $orderNum,
            'cardholder_name' => $request->cardholder_name,
            'amount' => $request->grandTotal,
            'stripe_payment_intent_id' => $charge->id,
            'stripe_payment_method' => $charge->payment_method ?? null,
            'stripe_currency' => $charge->currency,
            'stripe_client_secret' => $charge->client_secret ?? null,
            'stripe_status' => $charge->status,
        ]);
    } else {
        Invoice::create([
            'franchisee_id' => Auth::user()->franchisee_id,
            'name' => Auth::user()->name,
            'order_num' => $orderNum,
            'total_price' => $request->grandTotal,
            'payment_status' => 'unpaid',
            'direction' => 'payable',
            'due_date'  =>  Carbon::now()->addDays(7),
            'note' => mb_strimwidth($invoiceNotes, 0, 255, '...')
        ]);
    }


    // Send to ShipStation
   $shipStationPayload = [
    'order_number' =>  $orderNum,
    'order_email'  => Auth::user()->email,
    'ship_to_name' => $request->ship_to_name,
    'ship_to_address1' => $request->ship_to_address1,
    'ship_to_address2' => $request->ship_to_address2,
    'ship_to_city' => $request->ship_to_city,
    'ship_to_state' => $request->ship_to_state,
    'ship_to_zip' => $request->ship_to_zip,
    'ship_to_country' => $request->ship_to_country ?? 'US',
    'ship_to_phone' => $request->ship_to_phone,
    'is_paid' => $request->has('stripeToken'), // boolean
    'grandTotal' => $request->grandTotal,
    'invoice_id' => $invoice->id ?? null,
    'orderItems' => $orderItems, // shipstation-ready item array
];
    $isPaid = $request->has('stripeToken');
    $invoiceId = $orderNum ?? null;

    $shipstation = new ShipStationService();
    $shipstation->sendOrder($shipStationPayload, $isPaid, $invoiceId);


        // $orderTransaction = \App\Models\OrderTransaction::where('fgp_order_id',  $order->fgp_ordersID)->firstOrFail();
    // $orderDetails = \App\Models\FgpOrderDetail::where('fgp_order_id', $order->fgp_ordersID)->get();
    // $franchisee = \App\Models\Franchisee::where('franchisee_id', $order->user_ID)->firstOrFail();

   // $pdf = PDF::loadView('franchise_admin.payment.pdf.order-pos', compact('order', 'franchisee', 'orderDetails'));
   // $pdfPath = storage_path('app/public/order_invoice_' . $order->fgp_ordersID . '.pdf');
   // $pdf->save($pdfPath);

    // Send Email
   // $franchiseeadmin = \App\Models\User::where(['franchisee_id' => Auth::user()->franchisee_id , 'role' => 'franchise_admin'])->first();
   // if ($franchiseeadmin) {
   //     Mail::to($franchiseeadmin->email)->send(
   //         new \App\Mail\OrderPaidMail($franchiseeadmin, $order, $pdfPath, /* $paymentUrl */)
    //    );
    // }


   // unlink($pdfPath); // Remove the PDF after sending

return redirect()->route('franchise.orderpops.view')
        ->with('success', 'Order placed successfully ' . ($request->is_paid === '1' ? 'and paid!' : '. An invoice has been generated.'));

   // return redirect()->route('franchise.orderpops.view')->with('success', 'Order placed successfully!');
}




public function viewOrders()
{
     $orders = FgpOrder::with([
            'orderDetails.flavor',  // so `flavorSummary()` and `arrivedFlavorSummary()` don’t N+1
            'user',
            'customer',
        ])
        ->where('franchisee_id', Auth::user()->franchisee_id)
        ->orderByDesc('date_transaction')
        ->get()
        ->map(function ($order) {
            // Recompute total_amount (you may want to sum unit_number * unit_cost from details)
            $order->total_amount = $order->orderDetails->sum(fn($d) => $d->unit_number * $d->unit_cost);
            return $order;
        });



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

