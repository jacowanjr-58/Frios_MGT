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
use Yajra\DataTables\Facades\DataTables;

class OrderPopsController extends Controller
{
    public function index($franchisee=null)
    {
        $franchiseeId = intval($franchisee);
        $currentMonth = strval(Carbon::now()->format('n'));

        $pops = FgpItem::where('orderable', 1)
            ->where('internal_inventory', '>', 0)
            ->get()
            ->filter(function ($pop) use ($currentMonth) {
                $availableMonths = json_decode($pop->dates_available, true);
                return in_array($currentMonth, $availableMonths ?? []);
            });

        $totalPops = $pops->count();

        if (request()->ajax()) {
            return DataTables::of($pops)
                ->addColumn('checkbox', function($pop) {
                    return '<div class="form-check checkbox-secondary">
                        <input class="form-check-input pop-checkbox" type="checkbox"
                            value="'.$pop->fgp_item_id.'"
                            id="flexCheckDefault'.$pop->fgp_item_id.'">
                        <label class="form-check-label"
                            for="flexCheckDefault'.$pop->fgp_item_id.'"></label>
                    </div>';
                })
                ->addColumn('image', function($pop) {
                    if ($pop->image1) {
                        return '<img src="'.asset('storage/' . $pop->image1).'" alt="Image"
                            style="width: 50px; height: 50px; object-fit: contain;">';
                    }
                    return '<span>No Image</span>';
                })
                ->addColumn('price', function($pop) {
                    return '$'.number_format($pop->case_cost, 2);
                })
                ->addColumn('categories', function($pop) {
                    if($pop->categories->isNotEmpty()) {
                        $chunks = $pop->categories->pluck('name')->chunk(5);
                        $result = '';
                        foreach($chunks as $chunk) {
                            $result .= $chunk->join(', ') . '<br>';
                        }
                        return $result;
                    }
                    return 'No Category';
                })
                ->addColumn('stock_status', function($pop) {
                    return '<span class="badge bg-success">In Stock</span>';
                })
                ->addColumn('availability', function($pop) {
                    return '<span class="badge bg-success">Available</span>';
                })
                ->rawColumns(['checkbox', 'image', 'categories', 'stock_status', 'availability'])
                ->make(true);
        }

        return view('franchise_admin.orderpops.index', compact('pops', 'totalPops'));
    }

    public function create($franchisee=null)
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

        foreach ($items as &$item) {
            $item['case_cost'] = floatval(str_replace(['$', ','], '', $item['case_cost']));
            $item['quantity'] = $item['quantity'] ?? 1;
        }

        session(['ordered_items' => $items]);
        return redirect()->route('franchise.orderpops.confirm.page');
    }

    public function showConfirmPage($franchisee)
    {
        $franchiseeId = intval($franchisee);
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

    public function store(Request $request , $franchisee)
    {
        $franchiseeId = intval($franchisee);
        $minCases = 12;
        $factorCase = 3;

        $validated = $request->validate([
            'grandTotal' => 'required|numeric|min:1',
            'items' => 'required|array',
            'items.*.fgp_item_id' => 'required|exists:fgp_items,fgp_item_id',
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

        $totalCaseQty = collect($validated['items'])->sum('unit_number');

        if ($totalCaseQty < $minCases) {
            return redirect()->back()->withErrors(['Order must have at least ' . $minCases . ' cases.']);
        }

        if ($totalCaseQty % $factorCase !== 0) {
            return redirect()->back()->withErrors(['Order quantity must be a multiple of ' . $factorCase . '.']);
        }

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
                        'franchisee_id' => $franchiseeId,
                    ],
                ]);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['Stripe Error: ' . $e->getMessage()]);
            }
        }

        $order = FgpOrder::create([
            'user_ID' => Auth::user()->user_id,
            'franchisee_id' => $franchisee,
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

        $orderNum = 'FGP-' . $order->fgp_ordersID;

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

            $fgpItem = \App\Models\FgpItem::find($item['fgp_item_id']);

            $orderItems[] = [
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
            'is_paid' => $request->has('stripeToken'),
            'grandTotal' => $request->grandTotal,
            'invoice_id' => $invoice->id ?? null,
            'orderItems' => $orderItems,
        ];

        $isPaid = $request->has('stripeToken');
        $invoiceId = $orderNum ?? null;

        $shipstation = new ShipStationService();
        $shipstation->sendOrder($shipStationPayload, $isPaid, $invoiceId);

        return redirect()->route('franchise.orderpops.view')
            ->with('success', 'Order placed successfully ' . ($request->is_paid === '1' ? 'and paid!' : '. An invoice has been generated.'));
    }

    public function viewOrders($franchisee)
    {
        $franchiseeId = intval($franchisee);

        if (request()->ajax()) {
            $orders = FgpOrder::with([
                'orderDetails.flavor',
                'user',
                'customer',
            ])
            ->where('franchisee_id', $franchiseeId);

            return DataTables::of($orders)
                ->addColumn('order_number', function($order) {
                    return 'FGP-' . $order->fgp_ordersID;
                })
                ->addColumn('date', function($order) {
                    return Carbon::parse($order->date_transaction)->format('M d, Y');
                })
                ->addColumn('shipping', function($order) {
                    return $order->ship_to_name . '<br>' . 
                           $order->fullShippingAddress() . '<br>' . 
                           $order->ship_to_phone;
                })
                ->addColumn('tracking', function($order) {
                    if ($order->tracking_number) {
                        return '<a href="https://www.ups.com/track?tracknum=' . $order->tracking_number . '" target="_blank">' . 
                               $order->tracking_number . 
                               '</a>';
                    }
                    return '—';
                })
                ->addColumn('total', function($order) {
                    $total = $order->orderDetails->sum(fn($d) => $d->unit_number * $d->unit_cost);
                    return '$' . number_format($total, 2);
                })
                ->addColumn('flavors', function($order) {
                    return '<div><strong>Ordered:</strong> ' . $order->flavorSummary() . '</div>';
                })
                ->addColumn('paid_status', function($order) {
                    return $order->is_paid ? 
                        '<span class="badge bg-success">Paid</span>' : 
                        '<span class="badge bg-danger">Unpaid</span>';
                })
                ->addColumn('delivery_status', function($order) {
                    if ($order->is_delivered == 0) {
                        return '<form method="GET" action="' . route('franchise.inventory.confirm_delivery', ['franchisee' => request()->route('franchisee'), 'order' => $order->fgp_ordersID]) . '">
                            ' . csrf_field() . '
                            <button type="submit" class="btn btn-sm btn-outline-success">Confirm</button>
                        </form>';
                    }
                    return '<span class="badge bg-secondary">Completed</span>';
                })
                ->rawColumns(['shipping', 'tracking', 'flavors', 'paid_status', 'delivery_status'])
                ->make(true);
        }

        $totalOrders = FgpOrder::where('franchisee_id', $franchiseeId)->count();
        return view('franchise_admin.orderpops.vieworders', compact('totalOrders'));
    }

    public function customer($franchisee_id)
    {
        $customers = Customer::where('franchisee_id', $franchisee_id)->get();

        return response()->json([
            'data' => $customers,
        ]);
    }

    public function success(Request $request)
    {
        $sessionId = $request->get('session_id');

        if (!$sessionId) {
            return 'Missing Stripe session ID.';
        }

        \Stripe\Stripe::setApiKey(config('stripe.secret_key'));

        try {
            $session = StripeSession::retrieve($sessionId);
            $paymentIntent = \Stripe\PaymentIntent::retrieve($session->payment_intent);

            $orderId = $session->metadata->order_id ?? null;
            $franchiseeId = $session->metadata->franchisee_id ?? null;

            if (!$orderId || !$franchiseeId) {
                return 'Missing metadata from Stripe.';
            }

            $existing = OrderTransaction::where('stripe_payment_intent_id', $session->payment_intent)->first();
            if ($existing) {
                return 'Payment already recorded.';
            }

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

