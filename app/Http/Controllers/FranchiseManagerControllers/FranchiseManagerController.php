<?php

namespace App\Http\Controllers\FranchiseManagerControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FranchiseManagerController extends Controller
{
    public function dashboard()
    {
        return view('franchise_manager.dashboard');
    }
}
