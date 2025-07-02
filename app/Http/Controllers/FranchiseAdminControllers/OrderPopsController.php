<?php

namespace App\Http\Controllers\FranchiseAdminControllers;

use Carbon\Carbon;
use App\Models\FgpItem;
use App\Models\FgpOrder;
use App\Models\FgpCategory;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Franchise;
use App\Models\User;
use App\Services\ShipStationService;
use Illuminate\Http\Request;
use App\Models\AdditionalCharge;
use App\Models\FgpOrderCharge;
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

        $franchise = intval($franchisee);
        $currentMonth = strval(Carbon::now()->format('n'));

        $pops = FgpItem::where('orderable', 1)
            ->where('internal_inventory', '>', 0)
            ->get()
            ->filter(function ($pop) use ($currentMonth) {
                $availableMonths = $pop->dates_available;
                return in_array($currentMonth, $availableMonths ?? []);
            });
       
        $totalPops = $pops->count();
        // dd($totalPops);
        if (request()->ajax()) {
            return DataTables::of($pops)
                ->addColumn('checkbox', function($pop) {
                    return '<div class="form-check checkbox-secondary">
                        <input class="form-check-input pop-checkbox" type="checkbox"
                            value="'.$pop->id.'"
                            id="flexCheckDefault'.$pop->id.'">
                        <label class="form-check-label"
                            for="flexCheckDefault'.$pop->id.'"></label>
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

        return view('franchise_admin.orderpops.index', compact('pops', 'franchise', 'totalPops'));
    }

    public function create($franchisee=null)
    {

        $franchise = intval($franchisee);
        $currentMonth = (string) Carbon::now()->month;

        $categories = FgpCategory::with(['children.items' => function ($q) use ($currentMonth) {
            $q->select('fgp_items.id', 'fgp_items.name', 'fgp_items.image1', 'fgp_items.case_cost')
                ->where('orderable', 1)
                ->where('internal_inventory', '>', 0)
                ->whereJsonContains('dates_available', $currentMonth)
                ->orderBy('fgp_items.name');
        }])
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('franchise_admin.orderpops.create', compact('categories', 'franchise'));
    }

    public function confirmOrder(Request $request, $franchise)
    {
        // dd($franchise);
        $franchise = intval($franchise);
        $items = json_decode($request->input('ordered_items'), true);

        if (empty($items)) {
            return response()->json(['error' => 'No items selected for order.'], 400);
        }

        foreach ($items as &$item) {
            // Handle both 'price' and 'case_cost' keys from different sources
            if (isset($item['price']) && !isset($item['case_cost'])) {
                $item['case_cost'] = $item['price'];
                unset($item['price']);
            }
            
            $item['case_cost'] = floatval(str_replace(['$', ','], '', $item['case_cost'] ?? 0));
            $item['quantity'] = $item['quantity'] ?? 1;
        }
       
      
        session(['ordered_items' => $items]);
        return redirect()->route('franchise.orderpops.confirm.page', ['franchise' => $franchise]);
    }

    public function showConfirmPage($franchise)
    {
        $franchise = intval($franchise);
       
        // Since items are stored in sessionStorage (browser), we'll handle them via JavaScript
        // For now, return empty array and populate via JavaScript on page load
        $items = [];

        $customers = Customer::where('franchise_id', $franchise)->get();
        $franchisee = Franchise::where('id', $franchise)->first(); // Use first() instead of get()
        $requiredCharges = AdditionalCharge::where('charge_optional', 'required')->where('status', 1)->get();
        $optionalCharges = AdditionalCharge::where('charge_optional', 'optional')->where('status', 1)->get();

        return view('franchise_admin.orderpops.confirm', compact('items', 'requiredCharges', 'optionalCharges', 'customers', 'franchise', 'franchisee'));
    }

    public function store(Request $request, Franchise $franchise)
    {
       
        // $franchise = intval($franchise);
        $minCases = 12;
        $factorCase = 3;

        // Define base validation rules
        $rules = [
            'grandTotal' => 'required|numeric|min:1',
            'subtotal' => 'nullable|numeric|min:0',
            'items' => 'required|array',
            'items.*.fgp_item_id' => 'required|exists:fgp_items,id',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.unit_number' => 'required|integer|min:1',
            'ship_to_name' => 'required|string|max:255',
            'ship_to_address1' => 'required|string|max:255',
            'ship_to_address2' => 'nullable|string|max:255',
            'ship_to_city' => 'required|string|max:255',
            'ship_to_state' => 'required|string|max:255',
            'ship_to_zip' => 'required|string|max:20',
            'ship_to_phone' => 'nullable|string|max:20',
            'ship_to_country' => 'nullable|string|max:100',
            'ship_method' => 'nullable|string|max:100',
            'optional_charges' => 'nullable|array',
            'optional_charges.*' => 'nullable|numeric',
            'customer_id' => 'nullable|exists:customers,id',
            'payment_reference' => 'nullable|string|max:255',
        ];
       
        // Add payment validation rules if payment is being made
        if ($request->is_paid === '1') {
            $rules['stripeToken'] = 'required|string';
            $rules['cardholder_name'] = 'required|string|max:191';
        }

        // Validate the request first
        $validated = $request->validate($rules);
        
        // Use the validated franchise_id from the form
        // $franchiseeId = $validated['franchise_id'];
        
        Log::info('Using validated franchise_id: ' .  $franchise->id);

        // Get charges for order charge insertion
        $requiredCharges = AdditionalCharge::where('charge_optional', 'required')->where('status', 1)->get();
        $optionalCharges = AdditionalCharge::where('charge_optional', 'optional')->where('status', 1)->get();

        
        $totalCaseQty = collect($validated['items'])->sum('unit_number');

        if ($totalCaseQty < $minCases) {
            return redirect()->back()->withErrors(['Order must have at least ' . $minCases . ' cases.']);
        }

        // dd($totalCaseQty % $factorCase);
        if ($totalCaseQty % $factorCase !== 0) {
            return redirect()->back()->withErrors(['Order quantity must be a multiple of ' . $factorCase . '.']);
        }

        $charge = null;
        if ($request->is_paid === '1') {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

            try {
                $charge = \Stripe\Charge::create([
                    'amount' => $validated['grandTotal'] * 100,
                    'currency' => 'usd',
                    'description' => 'Order Payment by: ' . $validated['cardholder_name'],
                    'source' => $validated['stripeToken'],
                    'metadata' => [
                        'franchise_id' => $franchise->id,
                        'order_id' => $order->id ?? null,
                    ],
                ]);
            } catch (\Exception $e) {
                return redirect()->back()->withErrors(['Stripe Error: ' . $e->getMessage()]);
            }
        }
    

        $order = FgpOrder::create([
            'order_num' => 'FGP-' . time() . '-' . rand(1000, 9999),
            'franchise_id' => $franchise->id,
            'is_paid' => $request->is_paid === '1',
            'ship_to_name' => $validated['ship_to_name'],
            'ship_to_address1' => $validated['ship_to_address1'],
            'ship_to_address2' => $validated['ship_to_address2'],
            'ship_to_city' => $validated['ship_to_city'],
            'ship_to_state' => $validated['ship_to_state'],
            'ship_to_zip' => $validated['ship_to_zip'],
            'ship_to_country' => $validated['ship_to_country'] ?? 'US',
            'ship_to_phone' => $validated['ship_to_phone'],
            'ship_method' => $validated['ship_method'] ?? 'Standard',
            'shipstation_status' => 'awaiting_shipment',
        ]);

        $orderNum = 'FGP-' . $order->id;

        $orderItems = [];
        $noteLines[] = "Ordered Items:";
        $itemsSubtotal = 0;
        foreach ($validated['items'] as $index => $item) {
            DB::table('fgp_order_items')->insert([
                'fgp_order_id' => $order->id,
                'fgp_item_id' => $item['fgp_item_id'],
                'unit_price' => $item['unit_cost'],
                'quantity' => $item['unit_number'], 
                'price' => $item['unit_cost'] * $item['unit_number'],
                'created_at' => now(),
                'updated_at' => now(),
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
            $total = $item['unit_number'] * $item['unit_cost'];
            $itemsSubtotal += $total;

            $noteLines[] = "- {$name} | Qty: {$qty} | Cost: \${$cost} | Total: \$" . number_format($total, 2);
        }

        // Add subtotal and charges information to notes
        $noteLines[] = "\nItems Subtotal: \$" . number_format($itemsSubtotal, 2);
        
        // Handle optional charges if present
        if (!empty($validated['optional_charges'])) {
            $noteLines[] = "\nAdditional Charges:";
            $totalCharges = 0;
            
            foreach ($validated['optional_charges'] as $charge) {
                if (!empty($charge)) {
                    $chargeAmount = (float) $charge;
                    $totalCharges += $chargeAmount;
                    $noteLines[] = "- Additional Charge: \$" . number_format($chargeAmount, 2);
                }
            }
            
            if ($totalCharges > 0) {
                $noteLines[] = "Total Additional Charges: \$" . number_format($totalCharges, 2);
            }
        }
        
        $noteLines[] = "\nGrand Total: \$" . number_format($validated['grandTotal'], 2);
        
        // Add payment reference if provided
        if (!empty($validated['payment_reference'])) {
            $noteLines[] = "Payment Reference: " . $validated['payment_reference'];
        }

        $invoiceNotes = implode("\n", $noteLines);

        if ($request->is_paid === '1') {
            OrderTransaction::create([
                'franchise_id' => $franchise->id,
                'fgp_order_id' => $order->id,
                // 'order_num' => $orderNum,   
                'cardholder_name' => $validated['cardholder_name'],
                'amount' => $validated['grandTotal'],
                'stripe_payment_intent_id' => $charge->id,
                'stripe_payment_method' => $charge->payment_method ?? null,
                'stripe_currency' => $charge->currency,
                'stripe_client_secret' => $charge->client_secret ?? null,
                'stripe_status' => $charge->status,
            ]);
        } else {
            // Determine which entity to associate the invoice with (prioritize customer over franchise)
            $invoiceableType = null;
            $invoiceableId = null;
            
            if (!empty($validated['customer_id'])) {
                $customer = Customer::find($validated['customer_id']);
                if ($customer) {
                    $invoiceableType = Customer::class;
                    $invoiceableId = $customer->id;
                }
            }
            
            // If no customer, associate with franchise
            if (!$invoiceableType) {
                $franchise = Franchise::find($franchise->id);
                if ($franchise) {
                    $invoiceableType = Franchise::class;
                    $invoiceableId = $franchise->id;
                }
            }

            $invoice =Auth::user()->invoices()->create([
                'franchise_id' => $franchise->id, // Keep for backward compatibility
                'fgp_order_id' => $order->id,
                'name' => Auth::user()->name,
                'total_price' => $validated['grandTotal'],
                'payment_status' => 'unpaid',
                'direction' => 'payable',
                'tax_price' => 0,
                'due_date'  =>  Carbon::now()->addDays(7),
                'notes_internal' => mb_strimwidth($invoiceNotes, 0, 255, '...')
            ]);
          
        }

        $shipStationPayload = [
            'order_number' =>  $orderNum,
            'order_email'  => Auth::user()->email,
            'ship_to_name' => $validated['ship_to_name'],
            'ship_to_address1' => $validated['ship_to_address1'],
            'ship_to_address2' => $validated['ship_to_address2'],
            'ship_to_city' => $validated['ship_to_city'],
            'ship_to_state' => $validated['ship_to_state'],
            'ship_to_zip' => $validated['ship_to_zip'],
            'ship_to_country' => $validated['ship_to_country'] ?? 'US',
            'ship_to_phone' => $validated['ship_to_phone'],
            'is_paid' => $request->is_paid === '1',
            'grandTotal' => $validated['grandTotal'],
            'invoice_id' => $invoice->id ?? null,
            'orderItems' => $orderItems,
        ];

        $isPaid = $request->is_paid === '1';
        $invoiceId = $orderNum ?? null;

         $shipstation = new ShipStationService();
        $shipstation->sendOrder($shipStationPayload, $isPaid, $invoiceId);

        // Calculate totals for order and charges
        $additionalChargesTotal = 0;
        
        // Insert Required Charges into FgpOrderCharge table
        foreach ($requiredCharges as $charge) {
            $chargeAmount = $charge->charge_type === 'percentage' 
                ? ($itemsSubtotal * $charge->charge_price / 100)
                : $charge->charge_price;
                
            $additionalChargesTotal += $chargeAmount;
            
            FgpOrderCharge::create([
                'order_id' => $order->id,
                'charges_name' => $charge->charge_name,
                'charge_amount' => $chargeAmount,
                'charge_type' => $charge->charge_type,
            ]);
        }
        
        // Insert Optional Charges into fgp_order_charges table (matching selected checkboxes)
        if (!empty($validated['optional_charges'])) {
            foreach ($validated['optional_charges'] as $selectedChargePrice) {
                // Find the matching charge by price
                $matchingCharge = $optionalCharges->where('charge_price', $selectedChargePrice)->first();
                
                if ($matchingCharge !== null) {
                    $chargeAmount = $matchingCharge->charge_type === 'percentage' 
                        ? ($itemsSubtotal * $matchingCharge->charge_price / 100)
                        : $matchingCharge->charge_price;
                        
                    $additionalChargesTotal += $chargeAmount;
                    
                    FgpOrderCharge::create([
                        'order_id' => $order->id,
                        'charges_name' => $matchingCharge->charge_name,
                        'charge_amount' => $chargeAmount,
                        'charge_type' => $matchingCharge->charge_type,
                    ]);
                }
            }
        }
        
        // Update the order with correct amounts
        $order->update([
            'order_num' => $orderNum,
            'amount' => $itemsSubtotal,
            'additional_charges' => $additionalChargesTotal,
            'total_amount' => $itemsSubtotal + $additionalChargesTotal,
            // 'grand_total' => $validated['grandTotal'],
        ]);

        return redirect()->route('franchise.orders' , ['franchise' => $franchise->id])
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
            ->where('franchise_id', $franchiseeId);

            return DataTables::of($orders)
                ->addColumn('order_number', function($order) {
                    return 'FGP-' . $order->id;
                })
                ->addColumn('date', function($order) {
                    return Carbon::parse($order->created_at)->format('M d, Y');
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
                        return '<form method="GET" action="' . route('franchise.inventory.confirm_delivery', ['franchisee' => request()->route('franchisee'), 'order' => $order->id]) . '">
                            ' . csrf_field() . '
                            <button type="submit" class="btn btn-sm btn-outline-success">Confirm</button>
                        </form>';
                    }
                    return '<span class="badge bg-secondary">Completed</span>';
                })
                ->rawColumns(['shipping', 'tracking', 'flavors', 'paid_status', 'delivery_status'])
                ->make(true);
        }

        $totalOrders = FgpOrder::where('franchise_id', $franchiseeId)->count();
        return view('franchise_admin.orderpops.vieworders', compact('totalOrders'));
    }

    public function customer($franchise_id)
    {
        $customers = Customer::where('franchise_id', $franchise_id)->get();

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
            $franchiseeId = $session->metadata->franchise_id ?? null;

            if (!$orderId || !$franchiseeId) {
                return 'Missing metadata from Stripe.';
            }

            $existing = OrderTransaction::where('stripe_payment_intent_id', $session->payment_intent)->first();
            if ($existing) {
                return 'Payment already recorded.';
            }

            OrderTransaction::create([
                'franchise_id' => $franchiseeId,
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

