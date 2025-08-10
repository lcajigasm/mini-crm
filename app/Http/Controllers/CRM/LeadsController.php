<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;

class LeadsController extends Controller
{
    public function index()
    {
        return view('crm.leads.index');
    }
}





