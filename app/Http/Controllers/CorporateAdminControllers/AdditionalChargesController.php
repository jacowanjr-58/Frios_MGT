<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use App\Models\AdditionalCharge;
use Illuminate\Http\Request;

class AdditionalChargesController extends Controller
{
    public function index()
    {
        $additionalCharges = AdditionalCharge::all();
        $totalCharges = $additionalCharges->count();
    
        return view('corporate_admin.additional_charges.index', compact('additionalCharges', 'totalCharges'));
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

