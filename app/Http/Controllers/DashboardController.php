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

        // dd($user->getAllPermissions());
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
