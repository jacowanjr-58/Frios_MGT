<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\FgpItem;
use App\Models\InventoryAllocation;
use App\Models\ExpenseTransaction;
use App\Models\OrderTransaction;
use App\Models\EventTransaction;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use App\Models\FgpOrder;
use App\Models\FgpOrderDetail;
use App\Models\Franchise;
use App\Models\FranchiseEventItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;


class PaymentController extends Controller
{


    public function transaction($franchise){
        if (request()->ajax()) {
            $transactions = OrderTransaction::where('franchise_id', $franchise);

            return DataTables::of($transactions)
                ->addColumn('cardholder_name', function ($transaction) {
                    return $transaction->cardholder_name ?? 'N/A';
                })
                ->addColumn('amount', function ($transaction) {
                    return '$' . number_format($transaction->amount, 2);
                })
                ->addColumn('status', function ($transaction) {
                    $statusClass = match($transaction->stripe_status) {
                        'succeeded' => 'success',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'pending' => 'warning',
                        default => 'secondary'
                    };
                    return '<span class="badge bg-'.$statusClass.'">' . ucfirst($transaction->stripe_status) . '</span>';
                })
                ->addColumn('action', function ($transaction) {
                    $viewUrl = route('pos.order', $transaction->id);
                    $downloadUrl = route('order.pos.download', $transaction->id);
                    $actions = '<div class="d-flex">';
                    if (Auth::check() && Auth::user()->can('transactions.view')) {
                        $actions .= '<a href="'.$viewUrl.'" target="_blank" class="me-4">
                            <i class="ti ti-eye fs-20" style="color: #00ABC7;"></i>
                        </a>';
                    }
                    if (Auth::check() && Auth::user()->can('transactions.view')) {
                        $actions .= '<a href="'.$downloadUrl.'" class="me-4">
                            <i class="ti ti-file-download fs-20" style="color: #FF3131;"></i>
                        </a>';
                    }
                    $actions .= '</div>';
                    return $actions;
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();

        // $data['expenseAmount'] = [
        //     'daily' => ExpenseTransaction::whereDate('created_at', $today)->sum('amount'),
        //     'weekly' => ExpenseTransaction::whereBetween('created_at', [$startOfWeek, now()])->sum('amount'),
        //     'monthly' => ExpenseTransaction::whereBetween('created_at', [$startOfMonth, now()])->sum('amount'),
        //     'yearly' => ExpenseTransaction::whereBetween('created_at', [$startOfYear, now()])->sum('amount'),
        // ];

        $data['orderAmount'] = [
            'daily' => OrderTransaction::whereDate('created_at', $today)->sum('amount'),
            'weekly' => OrderTransaction::whereBetween('created_at', [$startOfWeek, now()])->sum('amount'),
            'monthly' => OrderTransaction::whereBetween('created_at', [$startOfMonth, now()])->sum('amount'),
            'yearly' => OrderTransaction::whereBetween('created_at', [$startOfYear, now()])->sum('amount'),
        ];

        // $data['eventAmount'] = [
        //     'daily' => EventTransaction::whereDate('created_at', $today)->sum('amount'),
        //     'weekly' => EventTransaction::whereBetween('created_at', [$startOfWeek, now()])->sum('amount'),
        //     'monthly' => EventTransaction::whereBetween('created_at', [$startOfMonth, now()])->sum('amount'),
        //     'yearly' => EventTransaction::whereBetween('created_at', [$startOfYear, now()])->sum('amount'),
        // ];

        $data['totalAmount'] = [
            'daily' => $data['orderAmount']['daily'],
            'weekly' => $data['orderAmount']['weekly'],
            'monthly' => $data['orderAmount']['monthly'],
            'yearly' => $data['orderAmount']['yearly'],
        ];


        $data['expenseTransactions'] = ExpenseTransaction::get();
        $data['orderTransactions'] = OrderTransaction::get();
        $data['eventTransactions'] = EventTransaction::get();
        $data['franchiseeId'] = request()->route('franchise');
        return view('corporate_admin.payment.transaction' , $data);
    }

    public function posExpense($id)
    {
        $data['expenseTransaction'] = ExpenseTransaction::where('id' , $id)->firstorfail();
        $data['expense'] = Expense::where('id' , $data['expenseTransaction']->expense_id)->firstorfail();
        $data['expenseCategory'] = ExpenseCategory::where('id' , $data['expense']->category_id)->firstorfail();
        $data['expenseSubCategory'] = ExpenseSubCategory::where('id' , $data['expense']->sub_category_id)->firstorfail();
        return view('corporate_admin.payment.expense-pos' , $data);
    }

    public function posDownloadPDF($id)
    {
       $expenseTransaction = ExpenseTransaction::where('id' , $id)->firstorfail();
       $expense = Expense::where('id' , $expenseTransaction->expense_id)->firstorfail();
       $expenseCategory = ExpenseCategory::where('id' , $expense->category_id)->firstorfail();
       $expenseSubCategory = ExpenseSubCategory::where('id' , $expense->sub_category_id)->firstorfail();

        $pdf = Pdf::loadView('corporate_admin.payment.pdf.expense-pos', compact('expenseTransaction', 'expense', 'expenseCategory', 'expenseSubCategory'));

        return $pdf->download('expense_invoice_friospos.pdf');
    }

    public function posOrder($id)
    {
       $data['orderTransaction'] = OrderTransaction::where('id' , $id)->firstorfail();
       $data['order'] = FgpOrder::where('id' , $data['orderTransaction']->fgp_order_id)->firstorfail();
       $data['franchise'] = Franchise::where('franchise_id' , $data['order']->user_ID)->firstorfail();
       $data['orderDetails'] = FgpOrderDetail::where('fgp_order_id' , $data['order']->id)->get();
        return view('corporate_admin.payment.order-pos' ,$data);
    }

    public function posOrderDownloadPDF($id)
    {
       $orderTransaction = OrderTransaction::where('id' , $id)->firstorfail();
       $order = FgpOrder::where('id' , $orderTransaction->fgp_order_id)->firstorfail();
       $franchise = Franchise::where('franchise_id' , $order->user_ID)->firstorfail();
       $orderDetails = FgpOrderDetail::where('fgp_order_id' , $order->id)->get();

        $pdf = Pdf::loadView('corporate_admin.payment.pdf.order-pos', compact('orderTransaction', 'order', 'franchise', 'orderDetails'));

        return $pdf->download('order_invoice_friospos.pdf');
    }


    public function posEvent($id)
    {
       $data['eventTransaction'] = EventTransaction::where('id' , $id)->firstorfail();
       $data['franchise'] = Franchise::where('franchise_id' , $data['eventTransaction']->franchise_id)->firstorfail();
        $data['eventItems'] = FranchiseEventItem::where('event_id' , $data['eventTransaction']->event_id)->get();
        return view('corporate_admin.payment.event-pos' ,$data);
    }

    public function posEventDownloadPDF($id)
    {
       $eventTransaction = EventTransaction::where('id' , $id)->firstorfail();
       $franchise = Franchise::where('franchise_id' , $eventTransaction->franchise_id)->firstorfail();
       $eventItems = FranchiseEventItem::where('event_id' , $eventTransaction->event_id)->get();

        $pdf = Pdf::loadView('corporate_admin.payment.pdf.event-pos', compact('eventTransaction', 'franchise', 'eventItems'));

        return $pdf->download('event_invoice_friospos.pdf');
    }
}
