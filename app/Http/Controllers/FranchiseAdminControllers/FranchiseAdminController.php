<?php

namespace App\Http\Controllers\FranchiseAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FranchiseAdminController extends Controller
{
    public function dashboard($franchisee = null)
    {

        $user = auth()->user();

        $franchiseeId = $franchisee;

        $startOfMonth = \Carbon\Carbon::now()->startOfMonth();

        $data['eventCount'] = \App\Models\Event::where('franchisee_id', $franchiseeId)->whereMonth('created_at', \Carbon\Carbon::now()->month)->count();
        $data['saleCount'] = \App\Models\InvoiceTransaction::where('franchisee_id', $franchiseeId)->whereMonth('created_at', \Carbon\Carbon::now()->month)->count();
        $data['events'] = \App\Models\Event::where('franchisee_id', $franchiseeId)->orderBy('created_at', 'DESC')->take(3)->get();
        $data['orderAmount'] = [
            'monthly' => \App\Models\OrderTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$startOfMonth, now()])->sum('amount'),
        ];
        $data['inoviceAmount'] = [
            'monthly' => \App\Models\InvoiceTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$startOfMonth, now()])->sum('amount'),
        ];
        $data['totalAmount'] = [
            'monthly' => $data['orderAmount']['monthly'] + $data['inoviceAmount']['monthly'],
        ];
        $data['salesData'] = \App\Models\InvoiceTransaction::where('franchisee_id', $franchiseeId)->whereYear('created_at', \Carbon\Carbon::now()->year)
            ->select(\DB::raw("COUNT(*) as count"), \DB::raw("MONTHNAME(created_at) as month"))
            ->groupBy(\DB::raw("MONTH(created_at), MONTHNAME(created_at)"))
            ->pluck('count', 'month');
        $data['franchiseeId'] = $franchiseeId;

        session(['franchisee_id' => $franchiseeId]);
        
        return view('dashboard', $data);
    }

    public function selectFranchisee()
    {
        $user = auth()->user();
        $franchisees = $user->franchisees;

        $franchiseeCount = $franchisees->count();

        if ($franchiseeCount === 0) {
            Auth::logout();
            request()->session()->regenerate();

            return redirect()
                ->route('login')
                ->with('error', 'You are not assigned to any franchise. Please contact your administrator.');
        }

        if ($franchiseeCount === 1) {
            $franchiseeId = $franchisees->first()->franchisee_id;

            return redirect()->route('franchise.dashboard', ['franchisee' => $franchiseeId]);
        }

        // More than one franchisee
        return view('franchise_admin.franchisee_select', compact('franchisees'));
    }


    public function setFranchisee(Request $request)
    {
        $request->validate(['franchisee_id' => 'required|exists:franchisees,franchisee_id']);
        return redirect("/franchise/{$request->franchisee_id}/dashboard");
        // return redirect()->route('franchise.dashboard', ['franchisee' => $request->franchisee_id]);
    }
}