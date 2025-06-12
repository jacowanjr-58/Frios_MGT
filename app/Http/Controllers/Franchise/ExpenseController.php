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
                            <i class="ti ti-edit fs-20" style="color: #FF7B31;"></i>
                        </a>
                        <form action="'.$deleteUrl.'" method="POST">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="ms-4 delete-expense">
                                <i class="ti ti-trash fs-20" style="color: #FF3131;"></i>
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
        $data['franchiseId'] = intval($franchisee);
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
        $data['franchiseId'] = intval($franchisee);
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
       
        $subCategories = ExpenseSubCategory::where('franchisee_id', $franchisee)->where('category_id', $category_id)->get();
        return response()->json([
            'data' => $subCategories,
        ]);
    }

    public function delete($franchisee, $id) {
        $expense = Expense::where('id', $id)->delete();
        return redirect()->route('franchise.expense', ['franchisee' => $franchisee])->with('success', 'Expense deleted successfully');
    }
}
