<?php

namespace App\Http\Controllers\Backend\User;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {

        return view('admin.user.user_dashboard');
    }
}
