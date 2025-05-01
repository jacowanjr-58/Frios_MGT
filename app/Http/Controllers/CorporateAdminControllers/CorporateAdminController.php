<?php

namespace App\Http\Controllers\CorporateAdminControllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CorporateAdminController extends Controller
{
    public function dashboard()
    {
        return view('corporate_admin.dashboard');
    }
}

