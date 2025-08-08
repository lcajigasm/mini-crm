<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Customer;

class ContactsController extends Controller
{
    public function index()
    {
        $customers = Customer::orderByDesc('created_at')->limit(10)->get();
        return view('crm.contacts.index', compact('customers'));
    }
}
