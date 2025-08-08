<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;

class MessagingController extends Controller
{
    public function index()
    {
        return view('crm.messaging.index');
    }
}



