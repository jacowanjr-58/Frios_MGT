<?php

namespace App\Http\Controllers\Franchise;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;

class CustomerController extends Controller
{
    public function index($franchisee) {
        if (request()->ajax()) {
            $customers = Customer::where('franchisee_id', $franchisee);

            return DataTables::of($customers)
                ->addColumn('formatted_date', function ($customer) {
                    return Carbon::parse($customer->created_at)->format('M d, Y');
                })
                ->addColumn('action', function ($customer) {
                    $viewUrl = route('franchise.customer.view', ['franchisee' => request()->route('franchisee'), 'id' => $customer->customer_id]);
                    $editUrl = route('franchise.customer.edit', ['franchisee' => request()->route('franchisee'), 'id' => $customer->customer_id]);
                    $deleteUrl = route('franchise.customer.delete', ['franchisee' => request()->route('franchisee'), 'id' => $customer->customer_id]);
                    
                    return '
                    <div class="d-flex">
                        <a href="'.$viewUrl.'">
                            <svg width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g id="SVGRepo_bgCarrier" stroke-width="0"></g><g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g><g id="SVGRepo_iconCarrier"> <path d="M9 4.45962C9.91153 4.16968 10.9104 4 12 4C16.1819 4 19.028 6.49956 20.7251 8.70433C21.575 9.80853 22 10.3606 22 12C22 13.6394 21.575 14.1915 20.7251 15.2957C19.028 17.5004 16.1819 20 12 20C7.81811 20 4.97196 17.5004 3.27489 15.2957C2.42496 14.1915 2 13.6394 2 12C2 10.3606 2.42496 9.80853 3.27489 8.70433C3.75612 8.07914 4.32973 7.43025 5 6.82137" stroke="#00ABC7" stroke-width="1.5" stroke-linecap="round"></path> <path d="M15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="#00ABC7" stroke-width="1.5"></path> </g></svg>
                        </a>
                        <a href="'.$editUrl.'" class="ms-4 edit-customer">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M17 3C17.2626 2.73735 17.5744 2.52901 17.9176 2.38687C18.2608 2.24473 18.6286 2.17157 19 2.17157C19.3714 2.17157 19.7392 2.24473 20.0824 2.38687C20.4256 2.52901 20.7374 2.73735 21 3C21.2626 3.26264 21.471 3.57444 21.6131 3.9176C21.7553 4.26077 21.8284 4.62856 21.8284 5C21.8284 5.37143 21.7553 5.73923 21.6131 6.08239C21.471 6.42555 21.2626 6.73735 21 7L7.5 20.5L2 22L3.5 16.5L17 3Z" stroke="#FF7B31" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                        <form action="'.$deleteUrl.'" method="POST">
                            '.csrf_field().'
                            '.method_field('DELETE').'
                            <button type="submit" class="ms-4 delete-customer">
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

        $data['customerCount'] = Customer::where('franchisee_id', $franchisee)->count();
        return view('franchise_admin.customer.index', $data);
    }

    public function create($franchisee) {
        return view('franchise_admin.customer.create');
    }

    public function store(Request $request, $franchisee) {
        $request->validate([
            'name' => 'required|max:191',
            'phone' => 'nullable|numeric|digits_between:8,16',
            'email' => 'required|email|max:191',
            'state' => 'nullable|alpha|size:2',
            'zip_code' => 'nullable|digits:5',
            'address1' => 'nullable|max:191',
            'address2' => 'nullable|max:191',
            'notes' => 'nullable|max:191',
        ]);

        $customer = Customer::create([
            'franchisee_id' => $franchisee,
            'user_id' => Auth::user()->user_id ?? 0,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'notes' => $request->notes,
        ]);

        return redirect()->route('franchise.customer', ['franchisee' => $franchisee])->with('success', 'Customer created successfully');
    }

    public function edit($franchisee, $id) {
        $data['customer'] = Customer::where('customer_id', $id)->firstOrFail();
        return view('franchise_admin.customer.edit', $data);
    }

    public function update(Request $request, $franchisee, $id) {
        $request->validate([
            'name' => 'required|max:191',
            'phone' => 'nullable|numeric|digits_between:8,16',
            'email' => 'required|email|max:191',
            'zip_code' => 'nullable|digits:5',
            'state' => 'nullable|alpha|size:2',
            'address1' => 'nullable|max:191',
            'address2' => 'nullable|max:191',
            'notes' => 'nullable|max:191',
        ]);

        $customer = Customer::where('customer_id', $id)->update([
            'franchisee_id' => $franchisee,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'state' => $request->state,
            'zip_code' => $request->zip_code,
            'address1' => $request->address1,
            'address2' => $request->address2,
            'notes' => $request->notes,
        ]);

        return redirect()->route('franchise.customer', ['franchisee' => $franchisee])->with('success', 'Customer updated successfully');
    }

    public function view($franchisee, $id) {
        $data['customer'] = Customer::where('customer_id', $id)->firstOrFail();
        return view('franchise_admin.customer.view', $data);
    }

    public function delete($franchisee, $id) {
        $data['customer'] = Customer::where('customer_id', $id)->delete();
        return redirect()->route('franchise.customer', ['franchisee' => $franchisee])->with('success', 'Customer deleted successfully');
    }
}
