<?php

namespace App\Http\Controllers;

use App\Models\Franchisee;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\OrderTransaction;
use App\Models\InvoiceTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard($franchisee = null)
    {

   
        $user = Auth::user();
        $userFranchisees = $user->franchisees;
        
        // Get the first franchisee ID for redirection
        $firstFranchiseeId = $userFranchisees->count() > 0 
            ? $userFranchisees->first()->franchisee_id 
            : Franchisee::first()->franchisee_id;
       
        // Redirect /dashboard to /franchise/9/dashboard
        if (request()->is('dashboard') && !$franchisee && ($user->hasRole('corporate_admin') )) {
            return redirect('/franchise/'.$firstFranchiseeId.'/dashboard');
        }

        $user = Auth::user();
        if ($user->hasRole('franchise_admin')) {
          
            return redirect('/franchise/select');
        }
        
        // If franchisee is provided, authorize and use it
        if ($franchisee) {
            if (!$user->franchisees->pluck('franchisee_id')->contains($franchisee)) {
                abort(403, 'Unauthorized.');
            }
            $franchiseeId = $franchisee;
        } else {
            $franchiseeId = $user->franchisee_id;
        }
        
        // Get default date range (current month)
        $dateRange = $this->getDateRange('month');
        
        // Get all dashboard data using centralized method
        $data = $this->getDashboardData($franchiseeId, $dateRange, 'month');

        if ($user->hasRole('corporate_admin')) {
            return view('dashboard.corporate_dashboard', $data);    
        } elseif ($user->hasRole('super_admin')) {
            return view('dashboard', $data);
        }

        return view('dashboard', $data);
    }

    public function filterDashboard(Request $request, $franchiseeId = null)
    {
        $user = Auth::user();
        
        // Handle franchise ID from route parameter or fallback to user's franchisee_id
        if ($franchiseeId) {
            // Use franchiseeId from route parameter
            $franchiseeId = $franchiseeId;
        } else {
            // Fallback to user's franchisee_id
            $franchiseeId = $user->franchisee_id;
        }
        
        // Get date range based on filter
        $dateRange = $this->getDateRange($request->filter, $request->from_date, $request->to_date);
        
        // Ensure current month filter shows current month data
        if ($request->filter === 'month') {
            $now = Carbon::now();
            $dateRange = [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now->copy()->endOfMonth()
            ];
        }
        
        // Get all dashboard data using centralized method
        $dashboardData = $this->getDashboardData($franchiseeId, $dateRange, $request->filter);
        
        return response()->json($dashboardData);
    }
    
    /**
     * Get all dashboard data with franchise context and date filtering
     */
    public function getDashboardData($franchiseeId, $dateRange, $filter = null)
    {
       
        // Basic counts and amounts
        $eventCount = Event::where('franchisee_id', $franchiseeId)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();
            
        $saleCount = InvoiceTransaction::where('franchisee_id', $franchiseeId)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();
            
        $orderAmount = OrderTransaction::where('franchisee_id', $franchiseeId)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->sum('amount');
            
        $invoiceAmount = InvoiceTransaction::where('franchisee_id', $franchiseeId)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->sum('amount');
            
        $totalAmount = $orderAmount + $invoiceAmount;
        
        // Enhanced Dashboard Insights
        
        // 1. Top 5 Flavors Ordered (franchise-specific)
        $topFlavors = DB::table('fgp_order_details')
            ->join('fgp_orders', 'fgp_order_details.fgp_order_id', '=', 'fgp_orders.fgp_ordersID')
            ->join('fgp_items', 'fgp_order_details.fgp_item_id', '=', 'fgp_items.fgp_item_id')
            ->select('fgp_items.name', 'fgp_items.image1', DB::raw('SUM(fgp_order_details.unit_number) as total_ordered'))
            ->where('fgp_orders.franchisee_id', $franchiseeId)
            ->where('fgp_orders.status', 'delivered')
            ->whereBetween('fgp_orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy('fgp_items.fgp_item_id', 'fgp_items.name', 'fgp_items.image1')
            ->orderBy('total_ordered', 'desc')
            ->limit(5)
            ->get();

         
            
        // 2. Events for this franchise (show future events)
        $events = Event::where('franchisee_id', $franchiseeId)
            ->where('start_date', '>=', Carbon::now()->startOfDay())
            ->orderBy('start_date', 'ASC')
            ->take(3)
            ->get();
            
        // 3. Calendar Events (show future events)
        $calendarEvents = Event::with('customer')
            ->where('franchisee_id', $franchiseeId)
            ->where('start_date', '>=', Carbon::now()->startOfDay())
            ->orderBy('start_date', 'asc')
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->event_name,
                    'start' => $event->start_date,
                    'end' => $event->end_date,
                    'status' => $event->event_status,
                    'customer' => $event->customer->name ?? 'No Customer',
                    'expected_sales' => $event->expected_sales,
                    'franchisee_id' => $event->franchisee_id
                ];
            });
        
        return [
            'eventCount' => $eventCount,
            'saleCount' => $saleCount,
            'totalAmount' => $totalAmount,
            'orderAmount' => ['monthly' => $orderAmount],
            'inoviceAmount' => ['monthly' => $invoiceAmount],
            'events' => $events,
            'topFlavors' => $topFlavors,
            'calendarEvents' => $calendarEvents,
            'couponCount' => InvoiceTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count() + OrderTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count(),
            'salesData' => $this->getSalesData($franchiseeId, $filter, $dateRange),
            'totalInventory' => DB::table('inventory_master')->sum('total_quantity'),
            'totalAllocated' => DB::table('franchise_event_items')
                ->join('events', 'franchise_event_items.event_id', '=', 'events.id')
                ->where('events.franchisee_id', $franchiseeId)
                ->where('events.start_date', '>=', $dateRange['start'])
                ->where('events.start_date', '<=', $dateRange['end'])
                ->sum('franchise_event_items.quantity'),
            'totalPopOrders' => DB::table('fgp_orders')
                ->where('franchisee_id', $franchiseeId)
                ->where('status', 'delivered')
                ->whereBetween('date_transaction', [$dateRange['start'], $dateRange['end']])
                ->count(),
            'totalFlavors' => DB::table('fgp_items')->count(),
            'totalFlavorCategories' => DB::table('fgp_categories')->count(),
            'totalExpenses' => DB::table('expenses')
                ->where('franchisee_id', $franchiseeId)
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->sum('amount'),
            'totalCharges' => DB::table('additionalcharges')
                ->where('franchisee_id', $franchiseeId)
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->sum('charge_price'),
            'totalCustomers' => DB::table('customers')
                ->where('franchisee_id', $franchiseeId)
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->count(),

              
            // Additional insights
            'totalAllocatedToEvents' => DB::table('franchise_event_items')
                ->join('events', 'franchise_event_items.event_id', '=', 'events.id')
                ->where('events.franchisee_id', $franchiseeId)
                ->where('events.start_date', '>=', $dateRange['start'])
                ->sum('franchise_event_items.quantity'),
            'monthlyPopOrders' => DB::table('fgp_orders')
                ->where('franchisee_id', $franchiseeId)
                ->where('status', 'delivered')
                ->whereMonth('date_transaction', Carbon::now()->month)
                ->whereYear('date_transaction', Carbon::now()->year)
                ->count(),
            'totalOrders' => InvoiceTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count() + OrderTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count(),
        ];
    }

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
    
    private function getSalesData($franchiseeId, $filter, $dateRange)
    {
        $query = InvoiceTransaction::where('franchisee_id', $franchiseeId)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
            
        if ($filter === 'today') {
            // Group by hour for today
            return $query->select(DB::raw("COUNT(*) as count"), DB::raw("HOUR(created_at) as hour"))
                ->groupBy(DB::raw("HOUR(created_at)"))
                ->pluck('count', 'hour')
                ->toArray();
        } elseif ($filter === 'week') {
            // Group by day for week
            return $query->select(DB::raw("COUNT(*) as count"), DB::raw("DAYNAME(created_at) as day"))
                ->groupBy(DB::raw("DAYOFWEEK(created_at), DAYNAME(created_at)"))
                ->pluck('count', 'day')
                ->toArray();
        } elseif ($filter === 'year') {
            // Group by month for year
            return $query->select(DB::raw("COUNT(*) as count"), DB::raw("MONTHNAME(created_at) as month"))
                ->groupBy(DB::raw("MONTH(created_at), MONTHNAME(created_at)"))
                ->pluck('count', 'month')
                ->toArray();
        } else {
            // Group by day for month or custom range
            return $query->select(DB::raw("COUNT(*) as count"), DB::raw("DAY(created_at) as day"))
                ->groupBy(DB::raw("DAY(created_at)"))
                ->pluck('count', 'day')
                ->toArray();
        }
    }

    public function loadMoreEvents(Request $request , $franchisee = null)
    {
        $page = $request->page ?? 1;
        if (Auth::user()->role == 'franchise_admin' || Auth::user()->role == 'franchise_manager' || Auth::user()->role == 'franchise_staff') {
            $events = Event::where('franchisee_id', $franchisee)
                ->where('start_date', '>=', Carbon::now()->startOfDay())
                ->orderBy('start_date', 'ASC')
                ->paginate(3, ['*'], 'page', $page);
        } elseif (Auth::user()->role == 'corporate_admin') {
            $events = Event::where('start_date', '>=', Carbon::now()->startOfDay())
                ->orderBy('start_date', 'ASC')
                ->paginate(3, ['*'], 'page', $page);
        }

        return response()->json([
            'html' => view('franchise_admin.event.upcoming', compact('events'))->render(),
            'next_page' => $events->nextPageUrl() ? $page + 1 : null
        ]);
    }


}
