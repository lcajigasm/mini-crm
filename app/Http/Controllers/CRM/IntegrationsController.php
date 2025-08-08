<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;

class IntegrationsController extends Controller
{
    public function index()
    {
        return view('crm.integrations.index');
    }
}



