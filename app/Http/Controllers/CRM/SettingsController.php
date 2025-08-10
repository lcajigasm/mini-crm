<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;

class SettingsController extends Controller
{
    public function index() { return view('crm.settings.index'); }
    public function featureFlags() { return view('crm.settings.feature-flags'); }

    public function audit()
    {
        $logs = AuditLog::orderByDesc('id')->limit(50)->get();
        return view('crm.settings.audit', compact('logs'));
    }

    public function securityChecklist()
    {
        return view('crm.settings.security');
    }
}
