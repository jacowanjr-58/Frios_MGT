<?php

namespace App\Http\Controllers\FranchiseStaffController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\User;
use App\Models\FgpItem;
use App\Models\InventoryAllocation;
use Illuminate\Support\Facades\Auth;
use App\Mail\InvoiceMail;
use App\Models\Stripe;
use Illuminate\Support\Facades\Mail;

class SaleController extends Controller
{
    public function index()
    {
        $data['invoices'] = Invoice::where('user_id', auth()->user()->id)->get();
        $data['invoiceCount'] = Invoice::where('user_id', auth()->user()->id)->count();

        return view('franchise_staff.sale.index', $data);
    }

    public function create()
    {
        $data['customers'] = Customer::where('franchise_id', Auth::user()->franchise_id)->get();
        $data['franchise'] = '10';
        $data['user'] = User::where('franchise_id', Auth::user()->franchise_id)->first();
        $flavors = FgpItem::all();

        $initialPopFlavors = [];
        foreach ($flavors as $flavor) {
            $initialPopFlavors[] = [
                'name' => $flavor->name,
                'image1' => $flavor->image1,
                'available' => $flavor->availableQuantity(),
            ];
        }

$data['allocations'] = \DB::table('inventory_allocations')
    ->join('inventory_master', 'inventory_allocations.inventory_id', '=', 'inventory_master.inventory_id')
            ->join('fgp_items',       'inventory_master.fgp_item_id',      '=', 'fgp_items.id')
    ->where('inventory_master.franchise_id', Auth::user()->franchise_id)
    ->select([
        'inventory_allocations.*',
        'fgp_items.name',
        'fgp_items.image1',
        'fgp_items.case_cost',
    ])
    ->get();


        return view('franchise_staff.sale.create', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'customer_id' => 'nullable',
            'note' => 'nullable|max:191',
            'items' => 'required|array',
            'items.*.flavor_id' => 'required|integer',
            'items.*.location' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::create([
            'franchise_id' => Auth::user()->franchise_id,
            'user_id' => auth()->user()->id,
            'customer_id' => $request->customer_id,
            'name' => $request->name,
            'note' => $request->note,
            'tax_price' => $request->tax_price,
            'total_price' => 0,
        ]);

        $total = 0;
        $taxRate = floatval($request->tax_price ?? 0);

        foreach ($request->items as $item) {
            $lineTotal = $item['quantity'] * $item['price'];
            $isTaxable = isset($item['taxable']) && $item['taxable'] === 'on';

            $taxAmount = $isTaxable ? ($lineTotal * ($taxRate / 100)) : 0;
            $finalLineTotal = $lineTotal + $taxAmount;

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'flavor_id' => $item['flavor_id'],
                'inventory_allocation_id' => $item['location'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'taxable' => $isTaxable,
                'total_price' => $lineTotal + $taxAmount,
            ]);

            $total += $finalLineTotal;
        }

        $invoice->update([
            'total_price' => $total,
        ]);


        if($request->customer_id) {
            $corporateAdmin = User::where('user_id', 17)->first();
            Mail::to($invoice->customer->email ?? $corporateAdmin->email)
                ->send(new \App\Mail\InvoiceMail($invoice, $invoice->items, $invoice->franchise->business_name));
        }


        return redirect()->route('franchise_staff.sales.index')->with('success', 'Sale created successfully.');
    }


    public function edit($id)
    {
        $invoice = Invoice::with('items.flavor')->findOrFail($id);

        $subtotal = $invoice->items->sum(function ($item) {
            return (float) $item->total_price;
        });

        $tax = 0;
        $taxRate = 10;
        if ($invoice->taxable) {
            $tax = $subtotal * ($taxRate / 100);
        }

        $total = $subtotal + $tax;

        $customers = Customer::where('franchise_id', Auth::user()->franchise_id)->get();

        $allocations = InventoryAllocation::with(['inventoryMaster.flavor'])
            ->whereHas('inventoryMaster', function ($q) {
                $q->where('franchise_id', Auth::user()->franchise_id);
            })
            ->get()
            ->map(function ($alloc) {
                return [
                    'id'           => $alloc->id,
                    'inventory_id' => $alloc->inventory_id,
                    'location_id'  => $alloc->location_id,
                    'allocated_quantity' => $alloc->allocated_quantity,
                    // Pull in the fields from fgp_items through inventoryMaster → flavor:
                    'name'         => optional($alloc->inventoryMaster->flavor)->name,
                    'image1'       => optional($alloc->inventoryMaster->flavor)->image1,
                    'case_cost'    => optional($alloc->inventoryMaster->flavor)->case_cost,
                ];
            });

        $franchise = $taxRate;

        return view('franchise_staff.sale.edit', compact(
            'invoice',
            'franchise',
            'customers',
            'allocations',
            'subtotal',
            'tax',
            'total'
        ));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'name' => 'required|string',
            'customer_id' => 'required|integer',
            'note' => 'nullable|max:191',
            'items' => 'required|array',
            'items.*.flavor_id' => 'required|integer',
            'items.*.location' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);
        $invoice->update([
            'franchise_id' => Auth::user()->franchise_id,
            'customer_id' => $request->customer_id,
            'name' => $request->name,
            'note' => $request->note,
            'tax_price' => $request->tax_price,
            'total_price' => 0,
        ]);

        $invoice->items()->delete();

        $total = 0;
        $taxRate = floatval($request->tax_price ?? 0);

        foreach ($request->items as $item) {
            $lineTotal = $item['quantity'] * $item['price'];
            $isTaxable = isset($item['taxable']) && $item['taxable'] === 'on';

            $taxAmount = $isTaxable ? ($lineTotal * ($taxRate / 100)) : 0;
            $finalLineTotal = $lineTotal + $taxAmount;

            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'flavor_id' => $item['flavor_id'],
                'inventory_allocation_id' => $item['location'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'taxable' => $isTaxable,
                'total_price' => $lineTotal + $taxAmount,
            ]);

            $total += $finalLineTotal;
        }

        $invoice->update([
            'total_price' => $total,
        ]);

        return redirect()->route('franchise_staff.sales.index')->with('success', 'Sale updated successfully.');
    }

    public function destroy($id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);

        $invoice->items()->delete();

        $invoice->delete();

        return redirect()->route('franchise_staff.sales.index')->with('success', 'Sale deleted successfully.');
    }



    public function show($id)
    {
        $invoice = Invoice::with('items.flavor')->findOrFail($id);

        $subtotal = $invoice->items->sum(function ($item) {
            return (float) $item->total_price;
        });

        $tax = 0;
        $taxRate = 10;
        if ($invoice->taxable) {
            $tax = $subtotal * ($taxRate / 100);
        }

        $total = $subtotal + $tax;

        $customers = Customer::where('franchise_id', Auth::user()->franchise_id)->get();

        $allocations = InventoryAllocation::with('flavor')
            ->join('fgp_items', 'fgp_items.id', '=', 'inventory_allocations.fgp_item_id')
            ->select(
                'inventory_allocations.*',
                'fgp_items.name',
                'fgp_items.image1',
                'fgp_items.case_cost'
            )
            ->get();

        $franchise = $taxRate;

        return view('franchise_staff.sale.view', compact(
            'invoice',
            'franchise',
            'customers',
            'allocations',
            'subtotal',
            'tax',
            'total'
        ));
    }
}
