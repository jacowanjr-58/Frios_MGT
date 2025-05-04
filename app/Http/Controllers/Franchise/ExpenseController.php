<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use App\Models\Expense;
use Auth;

class ExpenseController extends Controller
{
    public function index() {
        $data['expenses'] = Expense::where('franchisee_id' , Auth::id())->get();
        $data['expenseCount'] = Expense::where('franchisee_id' , Auth::id())->count();
        return view('franchise_admin.expense.index' ,$data);
    }

    public function create() {
        $data['ExpenseCategories'] = ExpenseCategory::get();
        return view('franchise_admin.expense.create' ,$data);
    }

    public function store(Request $request){
        $request->validate([
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'name' => 'string|max:191',
            'amount' => 'required|numeric',
            'date' => 'required',
        ]);

        $expense = Expense::create([
            'franchisee_id' => Auth::id(),
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'name' => $request->name,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);


        return redirect()->route('franchise.expense')->with('success' , 'Expense created successfully');
    }

    public function edit($id) {
        $data['expense'] = Expense::where('id' , $id)->first();
        $data['ExpenseCategories'] = ExpenseCategory::get();
        return view('franchise_admin.expense.edit' ,$data);
    }

    public function update(Request $request , $id){
        $request->validate([
            'category_id' => 'required',
            'sub_category_id' => 'required',
            'name' => 'string|max:191',
            'amount' => 'required|numeric',
            'date' => 'required',
        ]);

        $expense = Expense::where('id',$id)->update([
            'franchisee_id' => Auth::id(),
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'name' => $request->name,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);


        return redirect()->route('franchise.expense')->with('success' , 'Expense updated successfully');
    }

    public function getSubCategories($category_id)
    {
        $subCategories = ExpenseSubCategory::where('category_id', $category_id)->get();

        return response()->json([
            'data' => $subCategories,
        ]);
    }

    public function delete($id){
        $expense = Expense::where('id',$id)->delete();

        return redirect()->route('franchise.expense')->with('success' , 'Expense deleted successfully');


    }
}
