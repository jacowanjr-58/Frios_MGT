<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Auth;

class CustomerController extends Controller
{
    public function index() {
        $data['customers'] = Customer::where('franchisee_id' , Auth::user()->franchisee_id)->get();
        $data['customerCount'] = Customer::where('franchisee_id' , Auth::user()->franchisee_id)->count();
        return view('franchise_admin.customer.index' ,$data);
    }

    public function create() {
        return view('franchise_admin.customer.create');
    }

    public function store(Request $request){
        $request->validate([
            'name' => 'required|max:191',
            'phone' => 'required|numeric|digits_between:8,16',
            'email' => 'required|email|max:191',
    'state' => 'required|alpha|size:2', // 2-letter state code (alphabetic)
    'zip_code' => 'required|digits:5', // 5 digits zip code

            'address1' => 'required|max:191',
            'address2' => 'nullable|max:191',
        ]);

        $customer = Customer::create([
            'franchisee_id' => Auth::user()->franchisee_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'address1' => $request->address1,
            'address2' => $request->address2,
        ]);


        return redirect()->route('franchise.customer')->with('success' , 'Customer created successfully');
    }

    public function edit($id) {
        $data['customer'] = Customer::where('customer_id' , $id)->firstorfail();
        return view('franchise_admin.customer.edit' , $data);
    }

    public function update(Request $request , $id){
        $request->validate([
            'name' => 'required|max:191',
            'phone' => 'required|numeric|digits_between:8,16',
            'email' => 'required|email|max:191',
    'zip_code' => 'required|digits:5', // 5 digits zip code
    'state' => 'required|alpha|size:2', // 2-letter state code (alphabetic)

            'address1' => 'required|max:191',
            'address2' => 'nullable|max:191',
        ]);

        $customer = Customer::where('customer_id' , $id)->update([
            'franchisee_id' => Auth::user()->franchisee_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'address1' => $request->address1,
            'address2' => $request->address2,
        ]);


        return redirect()->route('franchise.customer')->with('success' , 'Customer updated successfully');
    }

    public function view($id) {
        $data['customer'] = Customer::where('customer_id' , $id)->firstorfail();
        return view('franchise_admin.customer.view' , $data);
    }

    public function delete($id) {
        $data['customer'] = Customer::where('customer_id' , $id)->delete();
        return redirect()->route('franchise.customer')->with('success' , 'Customer deleted successfully');
    }
}
