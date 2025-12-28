<?php

namespace App\Http\Controllers\Backend\Master;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.master.dashboard');
    }
}
