<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use App\Models\Expense;
use Stripe\Stripe;
use Stripe\Charge;
use App\Models\ExpenseTransaction;
use App\Models\User;
use App\Mail\ExpensePaidMail;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index($franchisee) {
        if (request()->ajax()) {
            $expenses = Expense::where('franchisee_id', $franchisee)
                ->with(['category', 'sub_category']);

            return DataTables::of($expenses)
                ->addColumn('category_name', function ($expense) {
                    return $expense->category->category ?? '-';
                })
                ->addColumn('subcategory_name', function ($expense) {
                    return $expense->sub_category->sub_category ?? '-';
                })
                ->addColumn('formatted_amount', function ($expense) {
                    return '$' . number_format($expense->amount, 2);
                })
                ->addColumn('formatted_date', function ($expense) {
                    return Carbon::parse($expense->date)->format('M d, Y');
                })
                ->addColumn('action', function ($expense) {
                    $editUrl = route('franchise.expense.edit', ['franchisee' => request()->route('franchisee'), 'id' => $expense->id]);
                    $deleteUrl = route('franchise.expense.delete', ['franchisee' => request()->route('franchisee'), 'id' => $expense->id]);
                    
                    return '
                    <div class="d-flex">
                        <a href="'.$editUrl.'" class="ms-4 edit-expense">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 3C17.2626 2.73735 17.5744 2.52901 17.9176 2.38687C18.2608 2.24473 18.6286 2.17157 19 2.17157C19.3714 2.17157 19.7392 2.24473 20.0824 2.38687C20.4256 2.52901 20.7374 2.73735 21 3C21.2626 3.26264 21.471 3.57444 21.6131 3.9176C21.7553 4.26077 21.8284 4.62856 21.8284 5C21.8284 5.37143 21.7553 5.73923 21.6131 6.08239C21.471 6.42555 21.2626 6.73735 21 7L7.5 20.5L2 22L3.5 16.5L17 3Z" stroke="#FF7B31" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <form action="'.$deleteUrl.'" method="POST">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="ms-4 delete-expense">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3 6H5H21" stroke="#FF3131" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8 6V4C8 3.46957 8.21071 2.96086 8.58579 2.58579C8.96086 2.21071 9.46957 2 10 2H14C14.5304 2 15.0391 2.21071 15.4142 2.58579C15.7893 2.96086 16 3.46957 16 4V6M19 6V20C19 20.5304 18.7893 21.0391 18.4142 21.4142C18.0391 21.7893 17.5304 22 17 22H7C6.46957 22 5.96086 21.7893 5.58579 21.4142C5.21071 21.0391 5 20.5304 5 20V6H19Z" stroke="#FF3131" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $data['expenseCount'] = Expense::where('franchisee_id', $franchisee)->count();
        return view('franchise_admin.expense.index', $data);
    }

    public function create($franchisee) {
        $data['ExpenseCategories'] = ExpenseCategory::get();
        return view('franchise_admin.expense.create', $data);
    }

    public function store(Request $request, $franchisee) {
        $request->validate([
            'category_id' => 'required|integer',
            'sub_category_id' => 'required|integer',
            'name' => 'required|string|max:191',
            'amount' => 'required|numeric|min:0.5',
            'date' => 'required|date',
        ]);

        $expense = Expense::create([
            'franchisee_id' => $franchisee,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'name' => $request->name,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return redirect()->route('franchise.expense', ['franchisee' => $franchisee])->with('success', 'Expense created successfully!');
    }

    public function edit($franchisee, $id) {
        $data['expense'] = Expense::where('id', $id)->first();
        $data['ExpenseCategories'] = ExpenseCategory::get();
        return view('franchise_admin.expense.edit', $data);
    }

    public function update(Request $request, $franchisee, $id) {
        $request->validate([
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'name' => 'string|max:191',
            'amount' => 'required|numeric',
            'date' => 'required',
        ]);

        $expense = Expense::where('id', $id)->update([
            'franchisee_id' => $franchisee,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'name' => $request->name,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return redirect()->route('franchise.expense', ['franchisee' => $franchisee])->with('success', 'Expense updated successfully');
    }

    public function getSubCategories($franchisee, $category_id) {
        $subCategories = ExpenseSubCategory::where('category_id', $category_id)->get();
        return response()->json([
            'data' => $subCategories,
        ]);
    }

    public function delete($franchisee, $id) {
        $expense = Expense::where('id', $id)->delete();
        return redirect()->route('franchise.expense', ['franchisee' => $franchisee])->with('success', 'Expense deleted successfully');
    }
}
