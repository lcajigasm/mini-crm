<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Lead;

class DashboardController extends Controller
{
    public function __invoke()
    {
        $todayAppointmentsCount = Appointment::query()
            ->whereDate('scheduled_at', now()->toDateString())
            ->count();

        $newLeads24hCount = Lead::query()
            ->where('created_at', '>=', now()->subDay())
            ->count();

        $noShowWeekCount = Appointment::query()
            ->where('status', 'no_show')
            ->whereBetween('scheduled_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->count();

        return view('crm.dashboard', compact('todayAppointmentsCount', 'newLeads24hCount', 'noShowWeekCount'));
    }
}
