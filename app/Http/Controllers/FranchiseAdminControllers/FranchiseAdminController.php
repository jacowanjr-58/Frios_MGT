<?php

namespace App\Http\Controllers\FranchiseAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FranchiseAdminController extends Controller
{
    public function dashboard()
    {
        return view('franchise_admin.dashboard');
    }
}