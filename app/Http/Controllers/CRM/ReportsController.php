<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;

class ReportsController extends Controller
{
    public function index()
    {
        return view('crm.reports.index');
    }
}



