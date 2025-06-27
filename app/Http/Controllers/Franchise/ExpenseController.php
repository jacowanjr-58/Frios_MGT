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
    public function index($franchise)
{
    if (request()->ajax()) {
        $expenses = Expense::where('franchise_id', $franchise)
            ->with(['category', 'sub_category']);

        return DataTables::of($expenses)
            ->addColumn('category_name', function ($expense) {
                return $expense->category->category ?? '-';
            })
            ->addColumn('subcategory_name', function ($expense) {
                return $expense->sub_category->category ?? '-';
            })
            ->addColumn('formatted_amount', function ($expense) {
                return '$' . number_format($expense->amount, 2);
            })
            ->addColumn('formatted_date', function ($expense) {
                return Carbon::parse($expense->date)->format('M d, Y');
            })
            ->addColumn('action', function ($expense) {
                $franchiseId = request()->route('franchise');

                $editUrl = route('franchise.expenses_by_franchise-edit', [
                    'franchise' => $franchiseId,
                    'id' => $expense->id,
                ]);

                $deleteUrl = route('franchise.expenses_by_franchise-delete', [
                    'franchise' => $franchiseId,
                    'id' => $expense->id,
                ]);

                return '
                    <div class="d-flex">
                        <a href="' . $editUrl . '" class="ms-4 edit-expense">
                            <i class="ti ti-edit fs-20" style="color: #FF7B31;"></i>
                        </a>
                        <form action="' . $deleteUrl . '" method="POST"">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="ms-4 delete-expense" style="border: none; background: transparent;">
                                <i class="ti ti-trash fs-20" style="color: #FF3131;"></i>
                            </button>
                        </form>
                    </div>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    $data['expenseCount'] = Expense::where('franchise_id', $franchise)->count();
    $franchiseId = intval($franchise);

    return view('franchise_admin.expense.index', $data, compact('franchiseId'));
}


    public function create($franchise) {
        $data['franchiseId'] = intval($franchise);
        $data['ExpenseCategories'] = ExpenseCategory::get();
        $franchiseId = intval($franchise);
        return view('franchise_admin.expense.create', $data ,compact('franchiseId'));
    }

    public function store(Request $request, $franchise) {

        // dd($request->all());
        $request->validate([
            // 'expense_category_id' => 'required|integer',
            // 'sub_category_id' => 'required|integer',
            'name' => 'required|string|max:191',
            'amount' => 'required|numeric|min:0.5',
            'date' => 'required|date',
        ]);

        $expense = Expense::create([
            'franchise_id' => $franchise,
            'expense_category_id' => $request->category_id,
            'expense_sub_category_id' => $request->sub_category_id,
            'name' => $request->name,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

            return redirect()->route('franchise.expenses_by_franchise', ['franchise' => $franchise])->with('success', 'Expense created successfully!'); 
    }

    public function edit($franchise, $id) {
      
        $data['expense'] = Expense::where('id', $id)->first();
        $data['franchiseId'] = intval($franchise);
        $data['ExpenseCategories'] = ExpenseCategory::get();
        return view('franchise_admin.expense.edit', $data);
    }

    public function update(Request $request, $franchise, $id) {
        $request->validate([
            // 'expense_category_id' => 'required',    
            // 'expense_sub_category_id' => 'required',
            'name' => 'string|max:191',
            'amount' => 'required|numeric',
            'date' => 'required',
        ]);

        $expense = Expense::where('id', $id)->update([
            'franchise_id' => $franchise,
            'expense_category_id' => $request->category_id,
            'expense_sub_category_id' => $request->sub_category_id,
            'name' => $request->name,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        return redirect()->route('franchise.expenses_by_franchise', ['franchise' => $franchise])->with('success', 'Expense updated successfully');
    }

    public function getSubCategories($franchise, $category_id) {
       
        $subCategories = ExpenseSubCategory::where('expense_category_id', $category_id)->get();
        return response()->json([
            'data' => $subCategories,
        ]);
    }

    public function delete($franchise, $id) {
        $expense = Expense::where('id', $id)->delete();
        return redirect()->route('franchise.expenses_by_franchise', ['franchise' => $franchise])->with('success', 'Expense deleted successfully');
    }
}
