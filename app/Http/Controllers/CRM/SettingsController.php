<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function featureFlags() { return view('crm.settings.feature-flags'); }
}
