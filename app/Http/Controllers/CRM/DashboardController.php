<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function __invoke()
    {
        return view('crm.dashboard');
    }
}
