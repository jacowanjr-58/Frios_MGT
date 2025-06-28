<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\FgpItem;
use Illuminate\Support\Facades\Http;
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
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\FranchiseEventItem;
use App\Models\Transaction;
use App\Models\Stripe as StripeModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Yajra\DataTables\DataTables;

class PaymentController extends Controller
{
    public function transaction($franchise)
    {
        $franchiseeId = intval($franchise);

        if (request()->ajax()) {
            if (request()->get('table') === 'invoice') {
                $invoices = Transaction::where('franchise_id', $franchiseeId)
                    ->with('invoice');

                return DataTables::of($invoices)
                    ->filter(function ($query) {
                        if (request()->has('search') && request()->get('search')['value'] != '') {
                            $searchValue = request()->get('search')['value'];
                            $query->whereHas('invoice', function($q) use ($searchValue) {
                                $q->where('name', 'like', "%{$searchValue}%");
                            })
                            ->orWhere('amount', 'like', "%{$searchValue}%")
                            ->orWhere('status', 'like', "%{$searchValue}%");
                        }
                    })
                    ->addColumn('name', function ($transaction) {
                        return $transaction->invoice->name ?? '-';
                    })
                    ->addColumn('amount', function ($transaction) {
                        return '$' . number_format($transaction->amount);
                    })
                    ->addColumn('status', function ($transaction) {
                        return $transaction->status ?? '-';
                    })
                    ->addColumn('action', function ($transaction) use ($franchise) {
                        return '
                            <div class="d-flex">
                                <a target="_blank" href="' . route('franchise.invoice.show', ['franchise' => $franchise, 'id' => $transaction->invoice_id]) . '" class="me-4">
                                    <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M9 4.45962C9.91153 4.16968 10.9104 4 12 4C16.1819 4 19.028 6.49956 20.7251 8.70433C21.575 9.80853 22 10.3606 22 12C22 13.6394 21.575 14.1915 20.7251 15.2957C19.028 17.5004 16.1819 20 12 20C7.81811 20 4.97196 17.5004 3.27489 15.2957C2.42496 14.1915 2 13.6394 2 12C2 10.3606 2.42496 9.80853 3.27489 8.70433C3.75612 8.07914 4.32973 7.43025 5 6.82137" stroke="#00ABC7" stroke-width="1.5" stroke-linecap="round"></path> <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="#00ABC7" stroke-width="1.5"></path> </g></svg>
                                </a>
                                <a href="' . route('franchise.invoice.pos.download', ['franchise' => $franchise, 'id' => $transaction->invoice_id]) . '" >
                                    <svg xmlns="http://www.w3.org/2000/svg" aria-label="PDF" role="img" viewBox="0 0 512 512" width="24px" height="24px" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><rect width="512" height="512" rx="15%" fill="#c80a0a"></rect><path fill="#ffffff" d="M413 302c-9-10-29-15-56-15-16 0-33 2-53 5a252 252 0 0 1-52-69c10-30 17-59 17-81 0-17-6-44-30-44-7 0-13 4-17 10-10 18-6 58 13 100a898 898 0 0 1-50 117c-53 22-88 46-91 65-2 9 4 24 25 24 31 0 65-45 91-91a626 626 0 0 1 92-24c38 33 71 38 87 38 32 0 35-23 24-35zM227 111c8-12 26-8 26 16 0 16-5 42-15 72-18-42-18-75-11-88zM100 391c3-16 33-38 80-57-26 44-52 72-68 72-10 0-13-9-12-15zm197-98a574 574 0 0 0-83 22 453 453 0 0 0 36-84 327 327 0 0 0 47 62zm13 4c32-5 59-4 71-2 29 6 19 41-13 33-23-5-42-18-58-31z"></path></g></svg>
                                </a>
                            </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            } else {
                $orders = OrderTransaction::where('franchise_id', $franchiseeId);

                return DataTables::of($orders)
                    ->filter(function ($query) {
                        if (request()->has('search') && request()->get('search')['value'] != '') {
                            $searchValue = request()->get('search')['value'];
                            $query->where(function($q) use ($searchValue) {
                                $q->where('cardholder_name', 'like', "%{$searchValue}%")
                                  ->orWhere('amount', 'like', "%{$searchValue}%")
                                  ->orWhere('stripe_status', 'like', "%{$searchValue}%");
                            });
                        }
                    })
                    ->addColumn('cardholder_name', function ($transaction) {
                        return $transaction->cardholder_name ?? '-';
                    })
                    ->addColumn('amount', function ($transaction) {
                        return '$' . number_format($transaction->amount);
                    })
                    ->addColumn('status', function ($transaction) {
                        return $transaction->stripe_status ?? '-';
                    })
                    ->addColumn('action', function ($transaction) use ($franchise) {
                        return '
                            <div class="d-flex">
                                <a target="_blank" href="' . route('franchise.pos.order', ['franchise' => $franchise, 'id' => $transaction->id]) . '" class="me-4">
                                    <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M9 4.45962C9.91153 4.16968 10.9104 4 12 4C16.1819 4 19.028 6.49956 20.7251 8.70433C21.575 9.80853 22 10.3606 22 12C22 13.6394 21.575 14.1915 20.7251 15.2957C19.028 17.5004 16.1819 20 12 20C7.81811 20 4.97196 17.5004 3.27489 15.2957C2.42496 14.1915 2 13.6394 2 12C2 10.3606 2.42496 9.80853 3.27489 8.70433C3.75612 8.07914 4.32973 7.43025 5 6.82137" stroke="#00ABC7" stroke-width="1.5" stroke-linecap="round"></path> <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="#00ABC7" stroke-width="1.5"></path> </g></svg>
                                </a>
                                <a href="' . route('franchise.order.pos.download', ['franchise' => $franchise, 'id' => $transaction->id]) . '" >
                                    <svg xmlns="http://www.w3.org/2000/svg" aria-label="PDF" role="img" viewBox="0 0 512 512" width="24px" height="24px" fill="#000000"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"><rect width="512" height="512" rx="15%" fill="#c80a0a"></rect><path fill="#ffffff" d="M413 302c-9-10-29-15-56-15-16 0-33 2-53 5a252 252 0 0 1-52-69c10-30 17-59 17-81 0-17-6-44-30-44-7 0-13 4-17 10-10 18-6 58 13 100a898 898 0 0 1-50 117c-53 22-88 46-91 65-2 9 4 24 25 24 31 0 65-45 91-91a626 626 0 0 1 92-24c38 33 71 38 87 38 32 0 35-23 24-35zM227 111c8-12 26-8 26 16 0 16-5 42-15 72-18-42-18-75-11-88zM100 391c3-16 33-38 80-57-26 44-52 72-68 72-10 0-13-9-12-15zm197-98a574 574 0 0 0-83 22 453 453 0 0 0 36-84 327 327 0 0 0 47 62zm13 4c32-5 59-4 71-2 29 6 19 41-13 33-23-5-42-18-58-31z"></path></g></svg>
                                </a>
                            </div>';
                    })
                    ->rawColumns(['action'])
                    ->make(true);
            }
        }

        $today = Carbon::today();
        $startOfWeek = Carbon::now()->startOfWeek();
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfYear = Carbon::now()->startOfYear();

        $data['orderAmount'] = [
            'daily' => OrderTransaction::where('franchise_id', $franchiseeId)->whereDate('created_at', $today)->sum('amount'),
            'weekly' => OrderTransaction::where('franchise_id', $franchiseeId)->whereBetween('created_at', [$startOfWeek, now()])->sum('amount'),
            'monthly' => OrderTransaction::where('franchise_id', $franchiseeId)->whereBetween('created_at', [$startOfMonth, now()])->sum('amount'),
            'yearly' => OrderTransaction::where('franchise_id', $franchiseeId)->whereBetween('created_at', [$startOfYear, now()])->sum('amount'),
        ];

        $data['inoviceAmount'] = [
            'daily' => Transaction::where('franchise_id', $franchiseeId)->whereDate('created_at', $today)->sum('amount'),
            'weekly' => Transaction::where('franchise_id', $franchiseeId)->whereBetween('created_at', [$startOfWeek, now()])->sum('amount'),
            'monthly' => Transaction::where('franchise_id', $franchiseeId)->whereBetween('created_at', [$startOfMonth, now()])->sum('amount'),
            'yearly' => Transaction::where('franchise_id', $franchiseeId)->whereBetween('created_at', [$startOfYear, now()])->sum('amount'),
        ];

        $data['totalAmount'] = [
            'daily' => $data['orderAmount']['daily'] + $data['inoviceAmount']['daily'],
            'weekly' => $data['orderAmount']['weekly'] + $data['inoviceAmount']['weekly'],
            'monthly' => $data['orderAmount']['monthly'] + $data['inoviceAmount']['monthly'],
            'yearly' => $data['orderAmount']['yearly'] + $data['inoviceAmount']['yearly'],
        ];

        $data['orderTransactions'] = OrderTransaction::where('franchise_id', $franchiseeId)->get();
        $data['invoiceTransactions'] = Transaction::where('franchise_id', $franchiseeId)->get();
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
        $data['order'] = FgpOrder::where('id', $data['orderTransaction']->fgp_order_id)->firstorfail();
        $data['franchise'] = Franchise::where('franchise_id', $data['order']->user_ID)->firstorfail();
        $data['orderDetails'] = FgpOrderDetail::where('fgp_order_id', $data['order']->id)->get();
        return view('franchise_admin.payment.order-pos', $data);
    }

    public function posOrderDownloadPDF($id)
    {
        $orderTransaction = OrderTransaction::where('id', $id)->firstorfail();
        $order = FgpOrder::where('id', $orderTransaction->fgp_order_id)->firstorfail();
        $franchise = Franchise::where('franchise_id', $order->user_ID)->firstorfail();
        $orderDetails = FgpOrderDetail::where('fgp_order_id', $order->id)->get();

        $pdf = Pdf::loadView('franchise_admin.payment.pdf.order-pos', compact('orderTransaction', 'order', 'franchise', 'orderDetails'));

        return $pdf->download('order_invoice_friospos.pdf');
    }


    public function posEvent($id)
    {
        $data['eventTransaction'] = EventTransaction::where('id', $id)->firstorfail();
        $data['franchise'] = Franchise::where('franchise_id', $data['eventTransaction']->franchise_id)->firstorfail();
        $data['eventItems'] = FranchiseEventItem::where('event_id', $data['eventTransaction']->event_id)->get();
        return view('franchise_admin.payment.event-pos', $data);
    }

    public function posEventDownloadPDF($id)
    {
        $eventTransaction = EventTransaction::where('id', $id)->firstorfail();
        $franchise = Franchise::where('franchise_id', $eventTransaction->franchise_id)->firstorfail();
        $eventItems = FranchiseEventItem::where('event_id', $eventTransaction->event_id)->get();

        $pdf = Pdf::loadView('franchise_admin.payment.pdf.event-pos', compact('eventTransaction', 'franchise', 'eventItems'));

        return $pdf->download('event_invoice_friospos.pdf');
    }

    public function posInvoiceDownloadPDF($id){
        $invoice = Invoice::where('id', $id)->firstorfail();
        $franchise = Franchise::where('franchise_id', $invoice->franchise_id)->firstorfail();
        $invoiceItems = InvoiceItem::where('invoice_id', $invoice->id)->get();

        $pdf = Pdf::loadView('franchise_admin.payment.pdf.invoice', compact('invoice', 'franchise', 'invoiceItems'));

        return $pdf->download('invoice_friospos.pdf');
    }

    public function stripe()
    {
        $data['stripe'] = StripeModel::where('id', Auth::user()->franchise_id)->first();
        return view('franchise_admin.stripe.index', $data);
    }

    public function stripePost(Request $request)
    {
        $request->validate([
            'public_key' => 'required',
            'secret_key' => 'required',
        ]);

        $response = Http::withBasicAuth($request->secret_key, '')
            ->get('https://api.stripe.com/v1/account');

        if ($response->successful()) {
            StripeModel::updateOrCreate(
                ['franchise_id' => Auth::user()->franchise_id],
                [
                    'public_key' => $request->public_key,
                    'secret_key' => $request->secret_key,
                ]
            );

            return redirect()->back()->with('success', 'Stripe credentials saved successfully.');
        } else {
            return redirect()->back()->with('error', 'Invalid Stripe API keys. Please check and try again.');
        }
    }

    public function success($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);


Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::retrieve($invoice->stripe_session_id);

        if ($session->payment_status === 'paid') {

            Transaction::create([
                'invoice_id' => $invoice->id,
                'franchise_id' => $invoice->franchise_id,
                'user_id' => $invoice->user_id ?? null,
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
