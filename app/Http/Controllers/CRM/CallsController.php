<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;

class CallsController extends Controller
{
    public function index()
    {
        return view('crm.calls.index');
    }
}



