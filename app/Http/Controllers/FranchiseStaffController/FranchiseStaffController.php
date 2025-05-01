<?php

namespace App\Http\Controllers\FranchiseStaffController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FranchiseStaffController extends Controller
{
    public function dashboard()
    {
        return view('franchise_staff.dashboard');
    }
}
