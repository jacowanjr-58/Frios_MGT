<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use App\Models\Expense;
use App\Models\Customer;

class ExpensesCategoryController extends Controller
{
    public function index(){
        $data['expenseSubCategories'] = ExpenseSubCategory::orderBy('created_at' , 'DESC')->get();
        $data['expenseSubCategoryCount'] = ExpenseSubCategory::count();
        return view('corporate_admin.expense.category.index' , $data);
    }


    public function create(){
        $data['ExpenseCategories'] = ExpenseCategory::get();
        return view('corporate_admin.expense.category.create' , $data);
    }

    public function edit($id){
        $data['ExpenseCategories'] = ExpenseCategory::get();
        $data['expenseSubCategory'] = ExpenseSubCategory::where('id' , $id)->first();
        return view('corporate_admin.expense.category.edit' , $data);
    }

    public function store(Request $request){
        $request->validate([
            'category' => 'string|max:191',
        ]);

        $category = ExpenseCategory::create([
            'category' => $request->category,
        ]);


        return redirect()->back()->with('success' , 'Expense Category created successfully');
    }


    public function Substore(Request $request){
        $request->validate([
            'category_id' => 'required',
            'sub_category' => 'string|max:191',
            'sub_category_description' => 'string|max:191',
        ]);

        $subCategory = ExpenseSubCategory::create([
            'category_id' => $request->category_id,
            'sub_category' => $request->sub_category,
            'sub_category_description' => $request->sub_category_description,
        ]);


        return redirect()->route('corporate_admin.expense-category')->with('success' , 'Expense Sub Category created successfully');
    }

    public function update(Request $request , $id){
        $request->validate([
            'category_id' => 'required',
            'sub_category' => 'string|max:191',
            'sub_category_description' => 'string|max:191',
        ]);

        $subCategory = ExpenseSubCategory::where('id',$id)->update([
            'category_id' => $request->category_id,
            'sub_category' => $request->sub_category,
            'sub_category_description' => $request->sub_category_description,
        ]);


        return redirect()->route('corporate_admin.expense-category')->with('success' , 'Expense Sub Category updated successfully');
    }

    public function delete($id){
        $category = ExpenseSubCategory::where('id' , $id)->delete();
        return redirect()->route('corporate_admin.expense-category')->with('success' , 'Expense Sub Category deleted successfully');
    }


    public function expense(){
        $data['expenses'] = Expense::get();
        $data['expenseCount'] = Expense::count();
        return view('corporate_admin.expense.index' ,$data);
    }

    public function customer() {
        $data['customers'] = Customer::get();
        $data['customerCount'] = Customer::count();
        return view('corporate_admin.customer.index' ,$data);
    }

    public function customerView($id) {
        $data['customer'] = Customer::where('customer_id' , $id)->firstorfail();
        return view('corporate_admin.customer.view' ,$data);
    }



    public function indexExpense(){
        $data['expenseSubCategories'] = ExpenseSubCategory::where('franchisee_id' , auth()->user()->franchisee_id)->orderBy('created_at' , 'DESC')->get();
        $data['expenseSubCategoryCount'] = ExpenseSubCategory::where('franchisee_id' , auth()->user()->franchisee_id)->count();
        return view('franchise_admin.expense.category.index' , $data);
    }


    public function createExpense(){
        $data['ExpenseCategories'] = ExpenseCategory::where('franchisee_id' , auth()->user()->franchisee_id)->get();
        return view('franchise_admin.expense.category.create' , $data);
    }

    public function editExpense($id){
        $data['ExpenseCategories'] = ExpenseCategory::where('franchisee_id' , auth()->user()->franchisee_id)->get();
        $data['expenseSubCategory'] = ExpenseSubCategory::where('id' , $id)->first();
        return view('franchise_admin.expense.category.edit' , $data);
    }

    public function storeExpense(Request $request){
        $request->validate([
            'category' => 'string|max:191',
        ]);

        $category = ExpenseCategory::create([
            'category' => $request->category,
            'franchisee_id' => auth()->user()->franchisee_id,
        ]);


        return redirect()->back()->with('success' , 'Expense Category created successfully');
    }


    public function SubstoreExpense(Request $request){
        $request->validate([
            'category_id' => 'required',
            'sub_category' => 'string|max:191',
            'sub_category_description' => 'string|max:191',
        ]);

        $subCategory = ExpenseSubCategory::create([
            'category_id' => $request->category_id,
            'sub_category' => $request->sub_category,
            'franchisee_id' => auth()->user()->franchisee_id,
            'sub_category_description' => $request->sub_category_description,
        ]);


        return redirect()->route('franchise.expense-category')->with('success' , 'Expense Sub Category created successfully');
    }

    public function updateExpense(Request $request , $id){
        $request->validate([
            'category_id' => 'required',
            'sub_category' => 'string|max:191',
            'sub_category_description' => 'string|max:191',
        ]);

        $subCategory = ExpenseSubCategory::where('id',$id)->update([
            'category_id' => $request->category_id,
            'sub_category' => $request->sub_category,
            'franchisee_id' => auth()->user()->franchisee_id,
            'sub_category_description' => $request->sub_category_description,
        ]);


        return redirect()->route('franchise.expense-category')->with('success' , 'Expense Sub Category updated successfully');
    }

    public function deleteExpense($id){
        $category = ExpenseSubCategory::where('id' , $id)->delete();
        return redirect()->route('franchise.expense-category')->with('success' , 'Expense Sub Category deleted successfully');
    }
}
