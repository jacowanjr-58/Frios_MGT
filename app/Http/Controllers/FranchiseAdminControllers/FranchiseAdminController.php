<?php

namespace App\Http\Controllers\FranchiseAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Franchise;
use App\Models\Event;
use App\Models\InvoiceTransaction;
use App\Models\OrderTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FranchiseAdminController extends Controller
{
    public function dashboard($franchise = null)
    {
        $franchiseId = $franchise;
        $user = Auth::user();
        $userFranchises = $user->franchises;
        
        // Get the first franchise ID for redirection
        $firstFranchiseId = $userFranchises?->count() > 0 
            ? $userFranchises->first()->franchise_id 
            : Franchise::first()->franchise_id;
    
        // Redirect /dashboard to /franchise/9/dashboard
        if (request()->is('dashboard') && !$franchise ) {
            return redirect('/franchise/'.$firstFranchiseId.'/dashboard');
        }

        // if ($user->hasRole('franchise_admin')) {
           
        //     return redirect('/franchise/'.$firstFranchiseId.'/dashboard');
           
        // }

        // Get default date range (current month)
        $dateRange = $this->getDateRange('month');
        
        // Get all dashboard data using centralized method from DashboardController
        $dashboardController = new \App\Http\Controllers\DashboardController();
        $data = $dashboardController->getDashboardData($franchiseId, $dateRange, 'month');

        session(['franchise_id' => $franchiseId]);
        
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

    public function selectFranchise()
    {
        $user = auth()->user();
        $franchises = $user->franchises;

        $franchiseCount = $franchises->count();

        if ($franchiseCount === 0) {
            Auth::logout();
            request()->session()->regenerate();

            return redirect()
                ->route('login')
                ->with('error', 'You are not assigned to any franchise. Please contact your administrator.');
        }

        if ($franchiseCount === 1) {
            $franchiseId = $franchises->first()->franchise_id;

            return redirect()->route('franchise.dashboard', ['franchise' => $franchiseId]);
        }

        // More than one franchise
        return view('franchise_admin.franchise_select', compact('franchises'));
    }


    public function setFranchise(Request $request)
    {
        $request->validate(['franchise_id' => 'required|exists:franchises,franchise_id']);
        // Store the selected franchise_id in session
        session(['franchise_id' => $request->franchise_id]);

        return redirect("/franchise/{$request->franchise_id}/dashboard");
        // return redirect()->route('franchise.dashboard', ['franchise' => $request->franchise_id]);
    }

    public function setSessionFranchise(Request $request)
    {
        $request->validate(['franchise_id' => 'required|exists:franchises,franchise_id']);

        session(['franchise_id' => $request->franchise_id]);

        return response()->json(['status' => 'success', 'message' => 'Franchise session updated.']);
    }
}