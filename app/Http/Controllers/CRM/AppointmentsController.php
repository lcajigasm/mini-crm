<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;

class AppointmentsController extends Controller
{
    public function index() { return view('crm.appointments.index'); }
}
