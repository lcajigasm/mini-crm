<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;

class DealsController extends Controller
{
    public function index() { return view('crm.deals.index'); }
}
