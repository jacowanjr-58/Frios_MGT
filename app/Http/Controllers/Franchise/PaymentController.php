<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
use App\Models\Franchisee;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\FranchiseEventItem;
use App\Models\InvoiceTransaction;
use App\Models\Stripe as StripeModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Auth;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
    public function transaction()
    {

        $franchiseeId = Auth::user()->franchisee_id;

        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();

        // $data['expenseAmount'] = [
        //     'daily' => ExpenseTransaction::where('franchisee_id', $franchiseeId)->whereDate('created_at', $today)->sum('amount'),
        //     'weekly' => ExpenseTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$startOfWeek, now()])->sum('amount'),
        //     'monthly' => ExpenseTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$startOfMonth, now()])->sum('amount'),
        //     'yearly' => ExpenseTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$startOfYear, now()])->sum('amount'),
        // ];

        $data['orderAmount'] = [
            'daily' => OrderTransaction::where('franchisee_id', $franchiseeId)->whereDate('created_at', $today)->sum('amount'),
            'weekly' => OrderTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$startOfWeek, now()])->sum('amount'),
            'monthly' => OrderTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$startOfMonth, now()])->sum('amount'),
            'yearly' => OrderTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$startOfYear, now()])->sum('amount'),
        ];

        $data['inoviceAmount'] = [
            'daily' => InvoiceTransaction::where('franchisee_id', $franchiseeId)->whereDate('created_at', $today)->sum('amount'),
            'weekly' => InvoiceTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$startOfWeek, now()])->sum('amount'),
            'monthly' => InvoiceTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$startOfMonth, now()])->sum('amount'),
            'yearly' => InvoiceTransaction::where('franchisee_id', $franchiseeId)->whereBetween('created_at', [$startOfYear, now()])->sum('amount'),
        ];

        $data['totalAmount'] = [
            'daily' => $data['orderAmount']['daily'] + $data['inoviceAmount']['daily'],
            'weekly' => $data['orderAmount']['weekly'] + $data['inoviceAmount']['weekly'],
            'monthly' => $data['orderAmount']['monthly'] + $data['inoviceAmount']['monthly'],
            'yearly' => $data['orderAmount']['yearly'] + $data['inoviceAmount']['yearly'],
        ];


        // $data['expenseTransactions'] = ExpenseTransaction::where('franchisee_id' , Auth::user()->franchisee_id)->get();
        $data['orderTransactions'] = OrderTransaction::where('franchisee_id', Auth::user()->franchisee_id)->get();
        $data['invoiceTransactions'] = InvoiceTransaction::where('franchisee_id', Auth::user()->franchisee_id)->get();
        return view('franchise_admin.payment.transaction', $data);
    }

    public function posExpense($id)
    {
        $data['expenseTransaction'] = ExpenseTransaction::where('id', $id)->firstorfail();
        $data['expense'] = Expense::where('id', $data['expenseTransaction']->expense_id)->firstorfail();
        $data['expenseCategory'] = ExpenseCategory::where('id', $data['expense']->category_id)->firstorfail();
        $data['expenseSubCategory'] = ExpenseSubCategory::where('id', $data['expense']->sub_category_id)->firstorfail();
        return view('franchise_admin.payment.expense-pos', $data);
    }

    public function posDownloadPDF($id)
    {
        $expenseTransaction = ExpenseTransaction::where('id', $id)->firstorfail();
        $expense = Expense::where('id', $expenseTransaction->expense_id)->firstorfail();
        $expenseCategory = ExpenseCategory::where('id', $expense->category_id)->firstorfail();
        $expenseSubCategory = ExpenseSubCategory::where('id', $expense->sub_category_id)->firstorfail();

        $pdf = Pdf::loadView('franchise_admin.payment.pdf.expense-pos', compact('expenseTransaction', 'expense', 'expenseCategory', 'expenseSubCategory'));

        return $pdf->download('expense_invoice_friospos.pdf');
    }

    public function posOrder($id)
    {
        $data['orderTransaction'] = OrderTransaction::where('id', $id)->firstorfail();
        $data['order'] = FgpOrder::where('fgp_ordersID', $data['orderTransaction']->fgp_order_id)->firstorfail();
        $data['franchisee'] = Franchisee::where('franchisee_id', $data['order']->user_ID)->firstorfail();
        $data['orderDetails'] = FgpOrderDetail::where('fgp_order_id', $data['order']->fgp_ordersID)->get();
        return view('franchise_admin.payment.order-pos', $data);
    }

    public function posOrderDownloadPDF($id)
    {
        $orderTransaction = OrderTransaction::where('id', $id)->firstorfail();
        $order = FgpOrder::where('fgp_ordersID', $orderTransaction->fgp_order_id)->firstorfail();
        $franchisee = Franchisee::where('franchisee_id', $order->user_ID)->firstorfail();
        $orderDetails = FgpOrderDetail::where('fgp_order_id', $order->fgp_ordersID)->get();

        $pdf = Pdf::loadView('franchise_admin.payment.pdf.order-pos', compact('orderTransaction', 'order', 'franchisee', 'orderDetails'));

        return $pdf->download('order_invoice_friospos.pdf');
    }


    public function posEvent($id)
    {
        $data['eventTransaction'] = EventTransaction::where('id', $id)->firstorfail();
        $data['franchisee'] = Franchisee::where('franchisee_id', $data['eventTransaction']->franchisee_id)->firstorfail();
        $data['eventItems'] = FranchiseEventItem::where('event_id', $data['eventTransaction']->event_id)->get();
        return view('franchise_admin.payment.event-pos', $data);
    }

    public function posEventDownloadPDF($id)
    {
        $eventTransaction = EventTransaction::where('id', $id)->firstorfail();
        $franchisee = Franchisee::where('franchisee_id', $eventTransaction->franchisee_id)->firstorfail();
        $eventItems = FranchiseEventItem::where('event_id', $eventTransaction->event_id)->get();

        $pdf = Pdf::loadView('franchise_admin.payment.pdf.event-pos', compact('eventTransaction', 'franchisee', 'eventItems'));

        return $pdf->download('event_invoice_friospos.pdf');
    }

    public function posInvoiceDownloadPDF($id){
        $invoice = Invoice::where('id', $id)->firstorfail();
        $franchisee = Franchisee::where('franchisee_id', $invoice->franchisee_id)->firstorfail();
        $invoiceItems = InvoiceItem::where('invoice_id', $invoice->id)->get();

        $pdf = Pdf::loadView('franchise_admin.payment.pdf.invoice', compact('invoice', 'franchisee', 'invoiceItems'));

        return $pdf->download('invoice_friospos.pdf');
    }

    public function stripe()
    {
        $data['stripe'] = StripeModel::where('franchisee_id', Auth::user()->franchisee_id)->first();
        return view('franchise_admin.stripe.index', $data);
    }

    public function stripePost(Request $request)
    {
        $request->validate([
            'public_key' => 'required',
            'secret_key' => 'required',
        ]);

        StripeModel::updateOrCreate(
            ['franchisee_id' => Auth::user()->franchisee_id],
            [
                'public_key' => $request->public_key,
                'secret_key' => $request->secret_key,
            ]
        );

        return redirect()->back()->with('success', 'Stripe credentials save successfully.');
    }

    public function success($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        $stripe = StripeModel::where('franchisee_id', $invoice->franchisee_id)->first();

        Stripe::setApiKey($stripe->secret_key);

        $session = Session::retrieve($invoice->stripe_session_id);

        if ($session->payment_status === 'paid') {

            InvoiceTransaction::create([
                'invoice_id' => $invoice->id,
                'franchisee_id' => $invoice->franchisee_id,
                'transaction_id' => $session->payment_intent,
                'status' => 'paid',
                'amount' => $invoice->total_price,
                'payment_method' => 'Stripe',
                'stripe_session_id' => $session->id,
            ]);

            return view('thankyou', compact('invoice'))
                ->with('message', 'Payment Successful! Your invoice has been paid.');
        }

        return view('thankyou', compact('invoice'))
            ->with('message', 'Payment not completed. Please try again.');
    }

    public function cancel($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        // Handle cancel logic
       dd('Payment Cancelled! Please try again.');
    }
}
