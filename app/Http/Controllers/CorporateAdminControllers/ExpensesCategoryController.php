<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ExpenseCategory;
use App\Models\ExpenseSubCategory;
use App\Models\Expense;
use App\Models\Customer;
use App\Models\Franchisee;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;

class ExpensesCategoryController extends Controller
{
    public function index(){
        
        if (request()->ajax()) {
            $expenseSubCategories = ExpenseSubCategory::query();

            return DataTables::of($expenseSubCategories)
                ->addColumn('category', function ($subCategory) {
                    return $subCategory->category->category ?? '-';
                })
                ->filterColumn('category', function ($query, $keyword) {
                    $query->whereHas('category', function ($q) use ($keyword) {
                        $q->where('category', 'like', "%$keyword%");
                    });
                })
                ->addColumn('action', function ($subCategory) {
                    $editUrl = route('corporate_admin.expense-category.edit', $subCategory->id);
                    $deleteUrl = route('corporate_admin.expense-sub-category.delete', $subCategory->id);

                    return '
                    <div class="d-flex">
                        <a href="'.$editUrl.'" class="edit-category">
                            <i class="ti ti-edit fs-20" style="color: #FF7B31;"></i>
                        </a>
                        <form action="'.$deleteUrl.'" method="POST" class="delete-form">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="ms-4 delete-category">
                                <i class="ti ti-trash fs-20" style="color: #FF3131;"></i>
                            </button>
                        </form>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $data['expenseSubCategoryCount'] = ExpenseSubCategory::count();
        return view('corporate_admin.expense.category.index', $data);
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
        if (request()->ajax()) {
            $expenses = Expense::query();

            return DataTables::of($expenses)
                ->addColumn('franchise', function ($expense) {
                    return $expense->franchisee->business_name ?? '-';
                })
                ->filterColumn('franchise', function ($query, $keyword) {
                    $query->whereHas('franchisee', function ($q) use ($keyword) {
                        $q->where('business_name', 'like', "%$keyword%");
                    });
                })
                ->addColumn('category', function ($expense) {
                    return $expense->category->category ?? '-';
                })
                ->filterColumn('category', function ($query, $keyword) {
                    $query->whereHas('category', function ($q) use ($keyword) {
                        $q->where('category', 'like', "%$keyword%");
                    });
                })
                ->addColumn('sub_category', function ($expense) {
                    return $expense->sub_category->sub_category ?? '-';
                })
                ->filterColumn('sub_category', function ($query, $keyword) {
                    $query->whereHas('sub_category', function ($q) use ($keyword) {
                        $q->where('sub_category', 'like', "%$keyword%");
                    });
                })
                ->addColumn('amount', function ($expense) {
                    return '$' . number_format($expense->amount);
                })
                ->addColumn('action', function ($expense) {
                    $editUrl = route('franchise.expense.edit', $expense->id);
                    $deleteUrl = route('franchise.expense.delete', $expense->id);

                    $actions = '<div class="d-flex">';
                    
                    // Edit button - check permission
                    if (auth()->check() && auth()->user()->can('expenses.by_franchisee')) {
                        $actions .= '<a href="'.$editUrl.'" class="edit-expense">
                            <i class="ti ti-edit fs-20" style="color: #FF7B31;"></i>
                        </a>';
                    }
                    
                    // Delete button - check permission
                    if (auth()->check() && auth()->user()->can('expenses.by_franchisee')) {
                        $actions .= '<form action="'.$deleteUrl.'" method="POST" class="delete-form">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="ms-4 delete-expense">
                                <i class="ti ti-trash fs-20" style="color: #FF3131;"></i>
                            </button>
                        </form>';
                    }
                    
                    $actions .= '</div>';
                    
                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $data['expenseCount'] = Expense::count();
        return view('corporate_admin.expense.index', $data);
    }

    public function customer() {
        if (request()->ajax()) {
            $customers = Customer::query();

            return DataTables::of($customers)
                ->addColumn('franchise', function ($customer) {
                    $franchisee = Franchisee::where('franchisee_id', $customer->franchisee_id)->first();
                    return $franchisee->business_name ?? '-';
                })
                ->filterColumn('franchise', function ($query, $keyword) {
                    $query->whereHas('franchisee', function ($q) use ($keyword) {
                        $q->where('business_name', 'like', "%$keyword%");
                    });
                })
                ->addColumn('action', function ($customer) {
                    $viewUrl = route('corporate_admin.customer.view', $customer->customer_id);

                    $actions = '<div class="d-flex">';
                    
                    // View button - check permission
                    if (auth()->check() && auth()->user()->can('customers.by_franchisee')) {
                        $actions .= '<a href="'.$viewUrl.'" class="view-customer">
                            <i class="ti ti-eye fs-20" style="color: #00ABC7;"></i>
                        </a>';
                    }
                    
                    $actions .= '</div>';
                    
                    return $actions;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $data['customerCount'] = Customer::count();
        return view('corporate_admin.customer.index', $data);
    }

    public function customerView($id) {
        $data['customer'] = Customer::where('customer_id' , $id)->firstorfail();
   
        return view('corporate_admin.customer.view' ,$data);
    }



    public function indexExpense($franchisee){
        if (request()->ajax()) {
            $expenseSubCategories = ExpenseSubCategory::where('franchisee_id', $franchisee)
                ->with('category'); // Eager load the category relationship

            return DataTables::of($expenseSubCategories)
                ->addColumn('main_category', function ($subCategory) {
                    return $subCategory->category->category ?? '-';
                })
                ->addColumn('action', function ($subCategory) use ($franchisee) {
                    return '
                    <div class="d-flex">
                        <a href="'.route('franchise.expense-category.edit', ['franchisee' => $franchisee, 'id' => $subCategory->id]).'" class="edit-expenseSubCategory">
                            <i class="ti ti-edit fs-20" style="color: #FF7B31;"></i>
                        </a>
                        <form action="'.route('franchise.expense-sub-category.delete', ['franchisee' => $franchisee, 'id' => $subCategory->id]).'" method="POST" class="delete-form">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="button" class="ms-4 delete-expense-category" 
                                data-id="'.$subCategory->id.'"
                                data-name="'.$subCategory->sub_category.'">
                                <i class="ti ti-trash fs-20" style="color: #FF3131;"></i>
                            </button>
                        </form>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $data['expenseSubCategories'] = ExpenseSubCategory::where('franchisee_id', $franchisee)->orderBy('created_at', 'DESC')->get();
        $data['expenseSubCategoryCount'] = ExpenseSubCategory::where('franchisee_id', $franchisee)->count();
        $data['franchisee'] = Franchisee::findOrFail($franchisee);
        return view('franchise_admin.expense.category.index', $data);
    }


    public function createExpense(){
        $data['ExpenseCategories'] = ExpenseCategory::where('franchisee_id' , Auth::user()->franchisee_id)->get();
        return view('franchise_admin.expense.category.create' , $data);
    }

    public function editExpense($franchisee,$id){
        
        $data['franchiseId'] = intval($franchisee);
        $data['ExpenseCategories'] = ExpenseCategory::where('franchisee_id' , $franchisee)->get();
        $data['expenseSubCategory'] = ExpenseSubCategory::where('id' , $id)->first();
       
        return view('franchise_admin.expense.category.edit' , $data);
    }

    public function storeExpense(Request $request){
        $request->validate([
            'category' => 'string|max:191',
        ]);

        $category = ExpenseCategory::create([
            'category' => $request->category,
            'franchisee_id' => Auth::user()->franchisee_id,
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
            'franchisee_id' => Auth::user()->franchisee_id,
            'sub_category_description' => $request->sub_category_description,
        ]);


        return redirect()->route('franchise.expense-category')->with('success' , 'Expense Sub Category created successfully');
    }

    public function updateExpense(Request $request, $franchisee, $id)
    {
        // Validate input
        $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'sub_category' => 'required|string|max:191',
            'sub_category_description' => 'nullable|string|max:191',
        ]);
    
        // Update the sub-category
        ExpenseSubCategory::where('id', $id)
            ->where('franchisee_id', $franchisee) // Optional safety check
            ->update([
                'category_id' => $request->category_id,
                'sub_category' => $request->sub_category,
                'sub_category_description' => $request->sub_category_description,
            ]);
            return redirect()->to("franchise/{$franchisee}/expense-category")
    ->with('success', 'Expense Sub Category updated successfully');

    
        // return redirect()
        //     ->route('expense-category', ['franchisee' => $franchisee])
        //     ->with('success', 'Expense Sub Category updated successfully');
    }
    

    public function deleteExpense($id){
        $category = ExpenseSubCategory::where('id' , $id)->delete();
        return redirect()->route('franchise.expense-category')->with('success' , 'Expense Sub Category deleted successfully');
    }
}
