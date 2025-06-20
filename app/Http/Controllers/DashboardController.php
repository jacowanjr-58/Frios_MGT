<?php

namespace App\Http\Controllers;

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
        $startOfMonth = Carbon::now()->startOfMonth();

        if ($user->hasRole('corporate_admin')) {
            $data['eventCount'] = Event::where('franchisee_id', $franchiseeId)->whereMonth('created_at', Carbon::now()->month)
                ->count();
            $data['saleCount'] = InvoiceTransaction::where('franchisee_id', $franchiseeId)->whereMonth('created_at', Carbon::now()->month)
                ->count();
            $data['events'] = Event::where('franchisee_id', $franchiseeId)->orderBy('created_at', 'DESC')->take(3)->get();
            $data['orderAmount'] = [
                'monthly' => OrderTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$startOfMonth, now()])->sum('amount'),
            ];
            $data['inoviceAmount'] = [
                'monthly' => InvoiceTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$startOfMonth, now()])->sum('amount'),
            ];
            $data['totalAmount'] = [
                'monthly' => $data['orderAmount']['monthly'] + $data['inoviceAmount']['monthly'],
            ];
            $data['salesData'] = InvoiceTransaction::where('franchisee_id', $franchiseeId)->whereYear('created_at', Carbon::now()->year)
                ->select(DB::raw("COUNT(*) as count"), DB::raw("MONTHNAME(created_at) as month"))
                ->groupBy(DB::raw("MONTH(created_at), MONTHNAME(created_at)"))
                ->pluck('count', 'month');
            
            // Enhanced Dashboard Insights
            
            // 1. Top 5 Flavors Ordered (across all franchises for corporate admin)
            $data['topFlavors'] = DB::table('fgp_order_details')
                ->join('fgp_orders', 'fgp_order_details.fgp_order_id', '=', 'fgp_orders.fgp_ordersID')
                ->join('fgp_items', 'fgp_order_details.fgp_item_id', '=', 'fgp_items.fgp_item_id')
                ->select('fgp_items.name', 'fgp_items.image1', DB::raw('SUM(fgp_order_details.unit_number) as total_ordered'))
                ->where('fgp_orders.status', 'delivered')
                ->groupBy('fgp_items.fgp_item_id', 'fgp_items.name', 'fgp_items.image1')
                ->orderBy('total_ordered', 'desc')
                ->limit(5)
                ->get();
                
            // 2. Total Pop Orders (across all franchises)
            $data['totalPopOrders'] = DB::table('fgp_orders')
                ->where('status', 'delivered')
                ->count();
                
            // 3. Total Sales (already in totalAmount but let's make it more explicit)
            $data['totalSales'] = [
                'current_month' => $data['totalAmount']['monthly'],
                'all_time' => OrderTransaction::sum('amount') + InvoiceTransaction::sum('amount')
            ];
            
            // 4. Franchise Events Calendar Data (all events for corporate admin view)
            $data['calendarEvents'] = Event::with('customer')
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
                
            // 5. Event Allocation Totals
            $data['eventAllocations'] = DB::table('franchise_event_items')
                ->join('events', 'franchise_event_items.event_id', '=', 'events.id')
                ->join('fgp_items', 'franchise_event_items.orderable', '=', 'fgp_items.fgp_item_id')
                ->select(
                    'events.event_name',
                    'events.start_date',
                    'fgp_items.name as flavor_name',
                    DB::raw('SUM(franchise_event_items.quantity) as total_allocated')
                )
                ->where('events.start_date', '>=', Carbon::now())
                ->groupBy('events.id', 'events.event_name', 'events.start_date', 'fgp_items.fgp_item_id', 'fgp_items.name')
                ->orderBy('events.start_date', 'asc')
                ->get();
                
            // 6. Inventory vs Event Allocation (Global View)
            $data['totalInventory'] = DB::table('inventory_master')
                ->sum('total_quantity');
                
            $data['totalAllocatedToEvents'] = DB::table('franchise_event_items')
                ->join('events', 'franchise_event_items.event_id', '=', 'events.id')
                ->where('events.start_date', '>=', Carbon::now())
                ->sum('franchise_event_items.quantity');
                
            $data['inventoryStatus'] = [
                'total_inventory' => $data['totalInventory'],
                'allocated_to_events' => $data['totalAllocatedToEvents'],
                'available_inventory' => $data['totalInventory'] - $data['totalAllocatedToEvents'],
                'allocation_percentage' => $data['totalInventory'] > 0 ? 
                    round(($data['totalAllocatedToEvents'] / $data['totalInventory']) * 100, 2) : 0
            ];
            
            // Additional insights for better dashboard experience
            $data['monthlyPopOrders'] = DB::table('fgp_orders')
                ->where('status', 'delivered')
                ->whereMonth('date_transaction', Carbon::now()->month)
                ->whereYear('date_transaction', Carbon::now()->year)
                ->count();
                
            // Dynamic Dashboard Totals
            
            // Total Orders (Dynamic) - from invoice transactions and order transactions
            $data['totalOrders'] = InvoiceTransaction::count() + OrderTransaction::count();
            
            // Total Flavors - from fgp_items
            $data['totalFlavors'] = DB::table('fgp_items')->count();
            
            // Total Flavor Categories - from fgp_categories  
            $data['totalFlavorCategories'] = DB::table('fgp_categories')->count();
            
            // Total Expenses - from expenses table
            $data['totalExpenses'] = DB::table('expenses')->sum('amount');
            
            // Total Charges - from additional_charges table
            $data['totalCharges'] = DB::table('additionalcharges')->sum('charge_price');
            
            // Total Customers - from users table (customers only)
            $data['totalCustomers'] = DB::table('customers')
              
                ->count();
                
            return view('dashboard.corporate_dashboard', $data);    
        } else {
            $data['eventCount'] = Event::where('franchisee_id', $franchiseeId)->whereMonth('created_at', Carbon::now()->month)
                ->count();
            $data['saleCount'] = InvoiceTransaction::where('franchisee_id', $franchiseeId)->whereMonth('created_at', Carbon::now()->month)
                ->count();
            $data['events'] = Event::where('franchisee_id', $franchiseeId)->orderBy('created_at', 'DESC')->take(3)->get();
            $data['orderAmount'] = [
                'monthly' => OrderTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$startOfMonth, now()])->sum('amount'),
            ];
            $data['inoviceAmount'] = [
                'monthly' => InvoiceTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$startOfMonth, now()])->sum('amount'),
            ];
            $data['totalAmount'] = [
                'monthly' => $data['orderAmount']['monthly'] + $data['inoviceAmount']['monthly'],
            ];
            $data['salesData'] = InvoiceTransaction::where('franchisee_id', $franchiseeId)->whereYear('created_at', Carbon::now()->year)
                ->select(DB::raw("COUNT(*) as count"), DB::raw("MONTHNAME(created_at) as month"))
                ->groupBy(DB::raw("MONTH(created_at), MONTHNAME(created_at)"))
                ->pluck('count', 'month');
        }

        // dd($data);
        return view('dashboard', $data);
    }

    public function filterDashboard(Request $request)
    {
        $user = Auth::user();
        $franchiseeId = $user->franchisee_id;
        
        // Get date range based on filter
        $dateRange = $this->getDateRange($request->filter, $request->from_date, $request->to_date);
        
        // Get filtered data
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
        
        // Get sales data for chart
        $salesData = $this->getSalesData($franchiseeId, $request->filter, $dateRange);
        
        return response()->json([
            'eventCount' => $eventCount,
            'saleCount' => $saleCount,
            'totalAmount' => $totalAmount,
            'couponCount' => InvoiceTransaction::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count() + OrderTransaction::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count(),
            'salesData' => $salesData,
            'totalInventory' => DB::table('inventory_master')->sum('total_quantity'),
            'totalAllocated' => DB::table('franchise_event_items')
                ->join('events', 'franchise_event_items.event_id', '=', 'events.id')
                ->where('events.start_date', '>=', $dateRange['start'])
                ->where('events.start_date', '<=', $dateRange['end'])
                ->sum('franchise_event_items.quantity'),
            'totalPopOrders' => DB::table('fgp_orders')
                ->where('status', 'delivered')
                ->whereBetween('date_transaction', [$dateRange['start'], $dateRange['end']])
                ->count(),
            'totalFlavors' => DB::table('fgp_items')->count(),
            'totalFlavorCategories' => DB::table('fgp_categories')->count(),
            'totalExpenses' => DB::table('expenses')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->sum('amount'),
            'totalCharges' => DB::table('additionalcharges')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->sum('charge_price'),
                        'totalCustomers' => DB::table('customers')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->count()
        ]);
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

    public function loadMoreEvents(Request $request)
    {
        $page = $request->page ?? 1;
        if (Auth::user()->role == 'franchise_admin' || Auth::user()->role == 'franchise_manager' || Auth::user()->role == 'franchise_staff') {
            $events = Event::where('franchisee_id', Auth::user()->franchisee_id)
                ->orderBy('created_at', 'DESC')
                ->paginate(3, ['*'], 'page', $page);
        } elseif (Auth::user()->role == 'corporate_admin') {
            $events = Event::orderBy('created_at', 'DESC')
                ->paginate(3, ['*'], 'page', $page);
        }

        return response()->json([
            'html' => view('franchise_admin.event.upcoming', compact('events'))->render(),
            'next_page' => $events->nextPageUrl() ? $page + 1 : null
        ]);
    }


}
