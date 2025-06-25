<?php

namespace App\Http\Controllers;

use App\Models\Franchise;
use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\OrderTransaction;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboard($franchise = null)
    {

        $user = Auth::user();
        $userFranchises = $user->franchises;
        // If franchise is provided, authorize and use it
        $firstFranchiseId = $userFranchises->isEmpty()
            ? $userFranchises->first()->id
            : Franchise::first()?->id;

        session(['franchise_id' => $firstFranchiseId]);

        if ($franchise) {
            if (!$user->franchises->pluck('id')->contains($franchise)) {
                abort(403, 'Unauthorized.');
            }
            $franchiseId = $franchise;
        } else {
            $franchiseId = $user->franchise_id;
        }
        // Get default date range (current month)
        $dateRange = $this->getDateRange('month');
        $data = $this->getDashboardData($franchiseId, $dateRange, 'month');
        if ($user->role == 'super_admin') {
            return view('dashboard', $data);
        }

        // Redirect /dashboard to /franchise/9/dashboard
        if (request()->is('dashboard') && !$franchise && ($user->hasRole('corporate_admin'))) {
            return redirect('/franchise/' . $firstFranchiseId . '/dashboard');
        }
        $user = Auth::user();
        if ($user->hasRole('franchise_admin')) {
            return redirect('/franchise/select');
        }
        // Get all dashboard data using centralized method
        if ($user->hasRole('corporate_admin')) {
            return view('dashboard.corporate_dashboard', $data);
        }
        return view('dashboard', $data);
    }

    public function filterDashboard(Request $request, $franchiseId = null)
    {
        $user = Auth::user();

        // Handle franchise ID from route parameter or fallback to user's franchise_id
        if ($franchiseId) {
            // Use franchiseId from route parameter
            $franchiseId = $franchiseId;
        } else {
            // Fallback to user's franchise_id
            $franchiseId = $user->franchise_id;
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
        $dashboardData = $this->getDashboardData($franchiseId, $dateRange, $request->filter);

        return response()->json($dashboardData);
    }

    /**
     * Get all dashboard data with franchise context and date filtering
     */
    public function getDashboardData($franchiseId, $dateRange, $filter = null)
    {
        // Basic counts and amounts
        $eventCount = Event::where('franchise_id', $franchiseId)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $saleCount = Transaction::where('franchise_id', $franchiseId)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->count();

        $orderAmount = Transaction::where('franchise_id', $franchiseId)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->sum('amount');

        $invoiceAmount = Transaction::where('franchise_id', $franchiseId)
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->sum('amount');

        $totalAmount = $orderAmount + $invoiceAmount;

        // Enhanced Dashboard Insights

        // 1. Top 5 Flavors Ordered (franchise-specific)
        $topFlavors = DB::table('fgp_order_items')
            ->join('fgp_orders', 'fgp_order_items.fgp_order_id', '=', 'fgp_orders.id')
            ->join('fgp_items', 'fgp_order_items.fgp_item_id', '=', 'fgp_items.id')
            ->select('fgp_items.name', 'fgp_items.image1', DB::raw('SUM(fgp_order_items.quantity) as total_ordered'))
            ->where('fgp_orders.franchise_id', $franchiseId)
            ->where('fgp_orders.status', 'delivered')
            ->whereBetween('fgp_orders.created_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy('fgp_items.id', 'fgp_items.name', 'fgp_items.image1')
            ->orderBy('total_ordered', 'desc')
            ->limit(5)
            ->get();

        // 2. Events for this franchise (show future events)
        $events = Event::where('franchise_id', $franchiseId)
            ->where('start_date', '>=', Carbon::now()->startOfDay())
            ->orderBy('start_date', 'ASC')
            ->take(3)
            ->get();

        // 3. Calendar Events (show future events)
        $calendarEvents = Event::with('customer')
            ->where('franchise_id', $franchiseId)
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
                    'franchise_id' => $event->franchise_id
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
            'couponCount' => Transaction::where('franchise_id', $franchiseId)->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count() + Transaction::where('franchise_id', $franchiseId)->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count(),
            'salesData' => $this->getSalesData($franchiseId, $filter, $dateRange),
            'totalInventory' => DB::table('inventory_master')->sum('total_quantity'),
            'totalAllocated' => DB::table('franchise_event_items')
                ->join('events', 'franchise_event_items.event_id', '=', 'events.id')
                ->where('events.franchise_id', $franchiseId)
                ->where('events.start_date', '>=', $dateRange['start'])
                ->where('events.start_date', '<=', $dateRange['end'])
                ->sum('franchise_event_items.quantity'),
            'totalPopOrders' => DB::table('fgp_orders')
                ->where('franchise_id', $franchiseId)
                ->where('status', 'delivered')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->count(),
            'totalFlavors' => DB::table('fgp_items')->count(),
            'totalFlavorCategories' => DB::table('fgp_categories')->count(),
            'totalExpenses' => DB::table('expenses')
                ->where('franchise_id', $franchiseId)
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->sum('amount'),
            'totalCharges' => DB::table('additional_charges')
                ->where('franchise_id', $franchiseId)
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->sum('charge_price'),
            'totalCustomers' => DB::table('customers')
                ->where('franchise_id', $franchiseId)
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->count()
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

    private function getSalesData($franchiseId, $filter, $dateRange)
    {
        $query = Transaction::where('franchise_id', $franchiseId)
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

    public function loadMoreEvents(Request $request, $franchise = null)
    {
        $page = $request->page ?? 1;
        if (Auth::user()->role == 'franchise_admin' || Auth::user()->role == 'franchise_manager' || Auth::user()->role == 'franchise_staff') {
            $events = Event::where('franchise_id', $franchise)
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
