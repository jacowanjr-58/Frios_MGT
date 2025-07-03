<?php
// PlaidController.php PLAID Integration
namespace App\Http\Controllers\PLAID;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\IncomeCategory;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use Carbon\Carbon;

class PlaidController extends Controller
{


    public function index(Request $request)
    {
        $franchiseId = Auth::user()->franchise_id;
        $query = BankTransaction::where('franchise_id', $franchiseId)
            ->with(['incomeCategory', 'expenseCategory', 'expenseSubCategory']);

        // Optional: Add filters
        if ($request->filled('category')) {
            $query->where('expense_category_id', $request->category)
                ->orWhere('income_category_id', $request->category);
        }
        if ($request->filled('type')) {
            if ($request->type === 'income') {
                $query->whereNotNull('income_category_id');
            } elseif ($request->type === 'expense') {
                $query->whereNotNull('expense_category_id');
            }
        }
        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('date', 'desc')->paginate(25);

        // For filter dropdowns
        $categories = ExpenseCategory::all();

        return view('plaid.index', compact('transactions', 'categories', 'franchiseId'));
    }

public function create()
{
    return view('plaid.create', [
        'incomeCategories' => IncomeCategory::all(),
        'expenseCategories' => ExpenseCategory::all(),
        'expenseSubCategories' => ExpenseSubCategory::all(),
    ]);
}

public function store(Request $request)
{
    $franchiseId = Auth::user()->franchise_id;
    $validated = $request->validate([
        'date' => 'required|date',
        'name' => 'required|string',
        'amount' => 'required|numeric',
        'type' => 'required|in:income,expense',
        'income_category_id' => 'nullable|exists:income_categories,id',
        'expense_category_id' => 'nullable|exists:expense_categories,id',
        'expense_sub_category_id' => 'nullable|exists:expense_sub_categories,id',
    ]);
    $data = [
        'franchise_id' => $franchiseId,
        'bank_account_id' => null, // or set if you want to link to a bank account
        'transaction_id' => uniqid('manual_'),
        'date' => $validated['date'],
        'name' => $validated['name'],
        'amount' => $validated['amount'],
        'income_category_id' => $validated['type'] === 'income' ? $validated['income_category_id'] : null,
        'expense_category_id' => $validated['type'] === 'expense' ? $validated['expense_category_id'] : null,
        'expense_sub_category_id' => $validated['type'] === 'expense' ? $validated['expense_sub_category_id'] : null,
    ];
    \App\Models\BankTransaction::create($data);
    return redirect()->route('transactions.index', ['franchise' => $franchiseId])->with('success', 'Transaction created.');
}

public function edit($id)
{
    $franchiseId = Auth::user()->franchise_id;
    $transaction = BankTransaction::where('franchise_id', $franchiseId)->findOrFail($id);
    return view('plaid.edit', [
        'transaction' => $transaction,
        'incomeCategories' => IncomeCategory::all(),
        'expenseCategories' => ExpenseCategory::all(),
        'expenseSubCategories' => ExpenseSubCategory::all(),
        'franchiseId' => $franchiseId
    ]);
}

public function update(Request $request, $id)
{
    $franchiseId = Auth::user()->franchise_id;
    $transaction =BankTransaction::where('franchise_id', $franchiseId)->findOrFail($id);
    $validated = $request->validate([
        'date' => 'required|date',
        'name' => 'required|string',
        'amount' => 'required|numeric',
        'type' => 'required|in:income,expense',
        'income_category_id' => 'nullable|exists:income_categories,id',
        'expense_category_id' => 'nullable|exists:expense_categories,id',
        'expense_sub_category_id' => 'nullable|exists:expense_sub_categories,id',
    ]);
    $transaction->update([
        'date' => $validated['date'],
        'name' => $validated['name'],
        'amount' => $validated['amount'],
        'income_category_id' => $validated['type'] === 'income' ? $validated['income_category_id'] : null,
        'expense_category_id' => $validated['type'] === 'expense' ? $validated['expense_category_id'] : null,
        'expense_sub_category_id' => $validated['type'] === 'expense' ? $validated['expense_sub_category_id'] : null,
    ]);
    return redirect()->route('transactions.index', ['franchise' => $franchiseId])->with('success', 'Transaction updated.');
}

public function destroy($id)
{
    $franchiseId = Auth::user()->franchise_id;
    $transaction = BankTransaction::where('franchise_id', $franchiseId)->findOrFail($id);
    $transaction->delete();
    return redirect()->route('transactions.index', ['franchise' => $franchiseId])->with('success', 'Transaction deleted.');
}


    public function createLinkToken(Request $request)
    {
        $response = Http::post('https://sandbox.plaid.com/link/token/create', [
            'client_id' => config('services.plaid.client_id'),
            'secret' => config('services.plaid.secret'),
            'client_name' => 'Your App Name',
            'country_codes' => ['US'],
            'language' => 'en',
            'user' => ['client_user_id' => Auth::id()],
            'products' => ['transactions'],
        ]);

        $linkToken = $response->json('link_token');
        return response()->json(['link_token' => $linkToken]);
    }

    // Step 2: Exchange public_token for access_token
    public function exchangePublicToken(Request $request)
    {
        $response = Http::post('https://sandbox.plaid.com/item/public_token/exchange', [
            'client_id' => config('services.plaid.client_id'),
            'secret' => config('services.plaid.secret'),
            'public_token' => $request->public_token,
        ]);
        $data = $response->json();
        $accessToken = $data['access_token'];
        $itemId = $data['item_id'];

        // Save to DB
        $account = BankAccount::create([
            'franchise_id' => Auth::user()->franchise_id,
            'access_token' => $accessToken,
            'item_id' => $itemId,
        ]);

        return response()->json(['success' => true]);
    }

    // Step 3: Fetch and store transactions
    public function fetchTransactions(Request $request)
{
    $account = BankAccount::where('franchise_id', Auth::user()->franchise_id)->firstOrFail();

    // Find the most recent transaction date for this account
    $latestDate = BankTransaction::where('franchise_id', $account->franchise_id)
        ->where('bank_account_id', $account->id)
        ->max('date');

    if ($latestDate) {
        $startDate = Carbon::parse($latestDate)->addDay()->format('Y-m-d');
    } else {
        $startDate = Carbon::now()->subMonths(2)->format('Y-m-d');
    }
    $endDate = Carbon::now()->format('Y-m-d');

    $response = Http::post('https://sandbox.plaid.com/transactions/get', [
        'client_id' => config('services.plaid.client_id'),
        'secret' => config('services.plaid.secret'),
        'access_token' => $account->access_token,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]);
    $data = $response->json();

    foreach ($data['transactions'] as $txn) {
        BankTransaction::updateOrCreate(
            ['transaction_id' => $txn['transaction_id']],
            [
                'franchise_id' => $account->franchise_id,
                'bank_account_id' => $account->id,
                'date' => $txn['date'],
                'name' => $txn['name'],
                'amount' => $txn['amount'],
                'category' => $txn['category'][0] ?? null,
                'sub_category' => $txn['category'][1] ?? null,
                'income_category_id' => null,
                'expense_category_id' => null,
                'expense_sub_category_id' => null,
            ]
        );
    }

    return response()->json(['success' => true]);
}


    public function showCategorize()
    {
        $franchiseId = Auth::user()->franchise_id;
        $transactions = BankTransaction::where('franchise_id', $franchiseId)
            ->where(function ($q) {
                $q->whereNull('income_category_id')
                    ->orWhereNull('expense_category_id');
            })
            ->get();
        return view('plaid.categorize', [
            'transactions' => $transactions,
            'incomeCategories' => IncomeCategory::all(),
            'expenseCategories' => ExpenseCategory::all(),
            'expenseSubCategories' => ExpenseSubCategory::all(),
        ]);
    }

    public function saveCategorize(Request $request)
    {
        foreach ($request->transactions as $id => $data) {
            $franchiseId = Auth::user()->franchise_id;
            $txn = BankTransaction::where('franchise_id', $franchiseId)->find($id);
            if ($txn) {
                $txn->income_category_id = $data['type'] === 'income' ? $data['income_category_id'] : null;
                $txn->expense_category_id = $data['type'] === 'expense' ? $data['expense_category_id'] : null;
                $txn->expense_sub_category_id = $data['type'] === 'expense' ? $data['expense_sub_category_id'] : null;
                $txn->save();
            }
        }
        return back()->with('success', 'Transactions categorized!');
    }

    public function pnl(Request $request)
    {
        $start_date = $request->input('start_date', now()->startOfMonth()->toDateString());
        $end_date = $request->input('end_date', now()->endOfMonth()->toDateString());

        $franchiseId = Auth::user()->franchise_id;
        $income = BankTransaction::where('franchise_id', $franchiseId)
            ->whereBetween('date', [$start_date, $end_date])
            ->whereNotNull('income_category_id')
            ->get()
            ->groupBy(fn($txn) => optional($txn->incomeCategory)->category ?? 'Uncategorized');

        $expenses = BankTransaction::where('franchise_id', $franchiseId)
            ->whereBetween('date', [$start_date, $end_date])
            ->whereNotNull('expense_sub_category_id')
            ->whereNotNull('expense_category_id')
            ->get()
            ->groupBy(fn($txn) => optional($txn->expenseCategory)->category ?? 'Uncategorized');

        return view('plaid.showP&L', compact('income', 'expenses', 'franchiseId', 'start_date', 'end_date'));
    }
}
