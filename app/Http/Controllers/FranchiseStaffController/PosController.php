<?php

namespace App\Http\Controllers\FranchiseStaffController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Carbon\Carbon;
use App\Models\OrderTransaction;
use App\Models\InvoiceTransaction;

class PosController extends Controller
{
    public function pos()
    {

        $userId = Auth::user()->user_id;

        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();

        // $data['expenseAmount'] = [
        //     'daily' => ExpenseTransaction::where('user_id', $userId)->whereDate('created_at', $today)->sum('amount'),
        //     'weekly' => ExpenseTransaction::where('user_id', $userId)->whereBetween('created_at', [$startOfWeek, now()])->sum('amount'),
        //     'monthly' => ExpenseTransaction::where('user_id', $userId)->whereBetween('created_at', [$startOfMonth, now()])->sum('amount'),
        //     'yearly' => ExpenseTransaction::where('franchisee_id', $userId)->whereBetween('created_at', [$startOfYear, now()])->sum('amount'),
        // ];

        $data['inoviceAmount'] = [
            'daily' => InvoiceTransaction::where('user_id', $userId)->whereDate('created_at', $today)->sum('amount'),
            'weekly' => InvoiceTransaction::where('user_id', $userId)->whereBetween('created_at', [$startOfWeek, now()])->sum('amount'),
            'monthly' => InvoiceTransaction::where('user_id', $userId)->whereBetween('created_at', [$startOfMonth, now()])->sum('amount'),
            'yearly' => InvoiceTransaction::where('user_id', $userId)->whereBetween('created_at', [$startOfYear, now()])->sum('amount'),
        ];

        $data['totalAmount'] = [
            'daily' => $data['inoviceAmount']['daily'],
            'weekly' => $data['inoviceAmount']['weekly'],
            'monthly' => $data['inoviceAmount']['monthly'],
            'yearly' => $data['inoviceAmount']['yearly'],
        ];


        // $data['expenseTransactions'] = ExpenseTransaction::where('user_id' , Auth::user()->user_id)->get();
        $data['invoiceTransactions'] = InvoiceTransaction::where('user_id', Auth::user()->user_id)->get();
        return view('franchise_staff.pos.transaction', $data);
    }
}
