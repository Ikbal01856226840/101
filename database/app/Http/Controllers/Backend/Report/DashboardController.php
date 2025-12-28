<?php

namespace App\Http\Controllers\Backend\Report;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    private $voucher;

    public function __construct()
    {

    }

    public function index()
    {
        return view('admin.report.report_dashboard');
    }
}
