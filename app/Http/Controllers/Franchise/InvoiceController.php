<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use App\Models\User;
use App\Models\FgpItem;
use App\Models\InventoryAllocation;
use App\Models\FgpOrderDetail;
use Illuminate\Support\Facades\Auth;
use App\Mail\InvoiceMail;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Facades\DataTables;

class InvoiceController extends Controller
{
    public function index($franchise)
    {
        if (request()->ajax()) {
            $invoices = Invoice::where('franchise_id', $franchise)
                ->with('customer');

            return DataTables::of($invoices)
                ->addColumn('type', function($invoice) {
                    if($invoice->direction === 'payable') {
                        return '<strong style="color:red;">PAY</strong>';
                    } elseif($invoice->direction === 'receivable') {
                        return '<strong style="color:green;">OWED</strong>';
                    }
                    return '-';
                })
                ->addColumn('action', function($invoice) use ($franchise) {
                    return '
                        <div class="d-flex">
                            <a href="'.route('franchise.invoice.show', ['franchise' => $franchise, 'id' => $invoice->id]).'" class="me-4">
                                <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M9 4.45962C9.91153 4.16968 10.9104 4 12 4C16.1819 4 19.028 6.49956 20.7251 8.70433C21.575 9.80853 22 10.3606 22 12C22 13.6394 21.575 14.1915 20.7251 15.2957C19.028 17.5004 16.1819 20 12 20C7.81811 20 4.97196 17.5004 3.27489 15.2957C2.42496 14.1915 2 13.6394 2 12C2 10.3606 2.42496 9.80853 3.27489 8.70433C3.75612 8.07914 4.32973 7.43025 5 6.82137" stroke="#00ABC7" stroke-width="1.5" stroke-linecap="round"></path> <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="#00ABC7" stroke-width="1.5"></path> </g></svg>
                            </a>
                            <a href="'.route('franchise.invoice.pos.download' , ['franchise' => $franchise, 'id' => $invoice->id]).'" class="me-4">
                                <svg xmlns="http://www.w3.org/2000/svg" aria-label="PDF" role="img" viewBox="0 0 512 512" width="24px" height="24px" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><rect width="512" height="512" rx="15%" fill="#c80a0a"></rect><path fill="#ffffff" d="M413 302c-9-10-29-15-56-15-16 0-33 2-53 5a252 252 0 0 1-52-69c10-30 17-59 17-81 0-17-6-44-30-44-7 0-13 4-17 10-10 18-6 58 13 100a898 898 0 0 1-50 117c-53 22-88 46-91 65-2 9 4 24 25 24 31 0 65-45 91-91a626 626 0 0 1 92-24c38 33 71 38 87 38 32 0 35-23 24-35zM227 111c8-12 26-8 26 16 0 16-5 42-15 72-18-42-18-75-11-88zM100 391c3-16 33-38 80-57-26 44-52 72-68 72-10 0-13-9-12-15zm197-98a574 574 0 0 0-83 22 453 453 0 0 0 36-84 327 327 0 0 0 47 62zm13 4c32-5 59-4 71-2 29 6 19 41-13 33-23-5-42-18-58-31z"></path></g></svg>
                            </a>
                            <form action="'.route('franchise.invoice.delete', ['franchise' => $franchise, 'id' => $invoice->id]).'" method="POST" class="delete-invoice-form">
                                '.csrf_field().'
                                '.method_field('DELETE').'
                                <button type="button" class="ms-4 delete-invoice" data-invoice-num="'.$invoice->order_num.'">
                                    <i class="ti ti-trash fs-20" style="color: #FF3131;"></i>
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['type', 'action'])
                ->make(true);
        }

        $data['invoiceCount'] = Invoice::where('franchise_id', $franchise)->count();
        return view('franchise_admin.payment.invoice.index', $data);
    }

    public function create($franchise)
    {
        $data['customers'] = Customer::where('franchise_id', $franchise)->get();
        $data['franchise'] = $franchise;

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
    ->join('fgp_items', 'fgp_items.id', '=', 'inventory_allocations.fgp_item_id')
    ->where('inventory_allocations.franchise_id', $franchise)
    ->select(
        'inventory_allocations.*',
        'fgp_items.name',
        'fgp_items.image1',
        'fgp_items.case_cost'
    )
    ->get();

        return view('franchise_admin.payment.invoice.create', $data);
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


        return redirect()->route('franchise.invoice.index')->with('success', 'Invoice created successfully.');
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

        return view('franchise_admin.payment.invoice.edit', compact(
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

        return redirect()->route('franchise.invoice.index')->with('success', 'Invoice updated successfully.');
    }
    public function destroy($franchise, $id)
    {
        $invoice = Invoice::with('items')->findOrFail($id);
        
        // Delete related items and the invoice
        $invoice->items()->delete();
        $invoice->delete();
    
        return redirect()
            ->route('franchise.invoice.index', ['franchise' => $franchise])
            ->with('success', 'Invoice deleted successfully.');
    }
    



    public function show($franchise, $id)
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

        return view('franchise_admin.payment.invoice.view', compact(
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
