<?php

namespace App\Http\Controllers\FranchiseAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Franchisee;
use App\Models\Event;
use App\Models\InvoiceTransaction;
use App\Models\OrderTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FranchiseAdminController extends Controller
{
    public function dashboard($franchisee = null)
    {
       
        $franchiseeId = $franchisee;
        $user = Auth::user();
        $userFranchisees = $user->franchisees;
        
        // Get the first franchisee ID for redirection
        $firstFranchiseeId = $userFranchisees->count() > 0 
            ? $userFranchisees->first()->franchisee_id 
            : Franchisee::first()->franchisee_id;
    
        // Redirect /dashboard to /franchise/9/dashboard
        if (request()->is('dashboard') && !$franchisee ) {
            return redirect('/franchise/'.$firstFranchiseeId.'/dashboard');
        }

        // if ($user->hasRole('franchise_admin')) {
           
        //     return redirect('/franchise/'.$firstFranchiseeId.'/dashboard');
           
        // }

        // Get default date range (current month)
        $dateRange = $this->getDateRange('month');
        
        // Get all dashboard data using centralized method from DashboardController
        $dashboardController = new \App\Http\Controllers\DashboardController();
        $data = $dashboardController->getDashboardData($franchiseeId, $dateRange, 'month');

        session(['franchisee_id' => $franchiseeId]);
        
        if ($user->hasRole('corporate_admin')) {
            return view('dashboard.corporate_dashboard', $data);    
        } else {
            // dd('s');
            return view('dashboard.franchise_admin_dashboard', $data);
        }
    }
    
    /**
     * Helper method to get date range (copied from DashboardController for consistency)
     */
    private function getDateRange($filter, $fromDate = null, $toDate = null)
    {
        $now = Carbon::now();
        
        switch ($filter) {
            case 'today':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay()
                ];
            case 'week':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek()
                ];
            case 'month':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
            case 'year':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear()
                ];
            case 'custom':
                return [
                    'start' => Carbon::parse($fromDate)->startOfDay(),
                    'end' => Carbon::parse($toDate)->endOfDay()
                ];
            default:
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
        }
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
        // Store the selected franchisee_id in session
        session(['franchisee_id' => $request->franchisee_id]);

        return redirect("/franchise/{$request->franchisee_id}/dashboard");
        // return redirect()->route('franchise.dashboard', ['franchisee' => $request->franchisee_id]);
    }

    public function setSessionFranchisee(Request $request)
    {
        $request->validate(['franchisee_id' => 'required|exists:franchisees,franchisee_id']);

        session(['franchisee_id' => $request->franchisee_id]);

        return response()->json(['status' => 'success', 'message' => 'Franchisee session updated.']);
    }
}