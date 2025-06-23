<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use App\Models\AdditionalCharge;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;

class AdditionalChargesController extends Controller
{
    public function index($franchisee)
    {
        $franchiseeID = $franchisee;
        if (request()->ajax()) {
            $charges = AdditionalCharge::where('franchisee_id', $franchiseeID);
            return DataTables::of($charges)
                ->addColumn('charge_amount', function ($charge) {
                    if ($charge->charge_type === 'percentage') {
                        return $charge->charge_price . '%';
                    }
                    return '$' . number_format($charge->charge_price, 2);
                })
                ->addColumn('charge_type', function ($charge) {
                    if ($charge->charge_type === 'percentage') {
                        return '<span>Percentage</span>';
                    }
                    return '<span>Fixed</span>';
                })
                ->addColumn('status', function ($charge) {
                    if (Auth::check() && Auth::user()->can('additional_charges.edit')) {
                        return '<label class="toggle-switch">
                            <input type="checkbox" class="toggle-input" data-id="'.$charge->additionalcharges_id.'"
                                '. ($charge->status ? 'checked' : '') .'>
                            <span class="slider"></span>
                        </label>';
                    } else {
                        return '<span class="badge '. ($charge->status ? 'bg-success' : 'bg-secondary') .'">'. ($charge->status ? 'Active' : 'Inactive') .'</span>';
                    }
                })
                ->addColumn('action', function ($charge) {
                    $actions = '<div class="d-flex">';
                    
                    // Edit action - check permission
                    if (Auth::check() && Auth::user()->can('additional_charges.edit')) {
                        $actions .= '<a href="'.route('additionalcharges.edit', $charge->additionalcharges_id).'" class="edit-user">
                            <i class="ti ti-edit fs-20" style="color: #FF7B31;"></i>
                        </a>';
                    }
                    
                    // Delete action - check permission
                    if (Auth::check() && Auth::user()->can('additional_charges.delete')) {
                        $actions .= '<form action="'.route('additionalcharges.destroy', $charge->additionalcharges_id).'" method="POST" style="display:inline;">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="ms-4 delete-charge">
                                <i class="ti ti-trash fs-20" style="color: #FF3131;"></i>
                            </button>
                        </form>';
                    }
                    
                    // If no permissions, show view-only message
                    if (!Auth::check() || (!Auth::user()->can('additional_charges.edit') && !Auth::user()->can('additional_charges.delete'))) {
                        // $actions .= '<span class="text-muted small">View Only</span>';
                    }
                    
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['charge_type', 'status', 'action'])
                ->make(true);
        }

        $totalCharges = AdditionalCharge::count();
        return view('corporate_admin.additional_charges.index', compact('totalCharges', 'franchiseeID'));
    }
    
    public function create()
    {
        return view('corporate_admin.additional_charges.create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'charge_name' => 'required|string|max:255',
            'charge_price' => 'required|numeric', // Allow both positive and negative numbers
            'charge_optional' => 'required|in:optional,required',
            'charge_type' => 'required|in:fixed,percentage',
        ]);
    
        // Handle validation for percentage charges
        if ($validated['charge_type'] == 'percentage') {
            // Check if the percentage is between -100% and 100%
            if ($validated['charge_price'] < -100 || $validated['charge_price'] > 100) {
                return back()->withErrors(['charge_price' => 'Percentage must be between -100% and 100%']);
            }
        }
    
        // Create the charge
        AdditionalCharge::create($validated);
    
        return redirect()->route('corporate_admin.additionalcharges.index')
            ->with('success', 'Additional charge added successfully.');
    }
    

    public function edit(AdditionalCharge $additionalcharges)
    {
        return view('corporate_admin.additional_charges.edit', compact('additionalcharges'));
    }

    public function update(Request $request, AdditionalCharge $additionalcharges)
    {
        $request->validate([
            'charge_name' => 'required|string|max:255',
            'charge_price' => 'required|numeric',
            'charge_type' => 'required|in:fixed,percentage',
            'charge_optional' => 'required|in:optional,required',
        ]);
    
        // If charge type is percentage, validate the range
        if ($request->charge_type == 'percentage') {
            if ($request->charge_price < -100 || $request->charge_price > 100) {
                return back()->withErrors(['charge_price' => 'The charge percentage must be between -100 and 100.']);
            }
        } else {
            // If it's a fixed charge, validate that the amount is non-negative
            if ($request->charge_price < 0) {
                return back()->withErrors(['charge_price' => 'The charge price must be a positive number.']);
            }
        }
    
        // Update the charge record
        $additionalcharges->update($request->all());
    
        return redirect()->route('corporate_admin.additionalcharges.index')
            ->with('success', 'Additional charge updated successfully.');
    }
    
    public function destroy(AdditionalCharge $additionalcharges)
    {
        $additionalcharges->delete();
        return redirect()->route('corporate_admin.additionalcharges.index')
            ->with('success', 'Additional charge deleted successfully.');
    }

    public function changeStatus(Request $request) {
        try {
            AdditionalCharge::findOrFail($request->chargesId)->update([
                'status' => $request->status == 'true' ? 1 : 0
            ]);
            return response()->json([
                'error' => false,
                'message' => "Status updated successfully"
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'error' => true,
                'message' => $th->getMessage()
            ]);
        }
    }
}

