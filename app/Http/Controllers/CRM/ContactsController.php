<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;

class ContactsController extends Controller
{
    public function index() { return view('crm.contacts.index'); }
}
