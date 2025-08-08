<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentRescheduleRequest;
use App\Http\Requests\AppointmentStoreRequest;
use App\Domain\Services\AgendaService;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;

class AppointmentsController extends Controller
{
    public function __construct(private readonly AgendaService $agenda)
    {
    }

    public function index()
    {
        $appointments = Appointment::with('customer')
            ->orderByDesc('scheduled_at')
            ->limit(10)
            ->get();

        return view('crm.appointments.index', compact('appointments'));
    }

    public function store(AppointmentStoreRequest $request): RedirectResponse
    {
        $this->agenda->create($request->validated());
        return redirect()->route('appointments.index')->with('status', 'Cita creada');
    }

    public function reschedule(Appointment $appointment, AppointmentRescheduleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->agenda->reschedule($appointment, Carbon::parse($data['scheduled_at']), $data['duration_minutes'] ?? ($appointment->duration_minutes ?? 30));
        return redirect()->route('appointments.index')->with('status', 'Cita reprogramada');
    }

    public function cancel(Appointment $appointment): RedirectResponse
    {
        $this->agenda->cancel($appointment);
        return redirect()->route('appointments.index')->with('status', 'Cita cancelada');
    }

    public function attend(Appointment $appointment): RedirectResponse
    {
        $this->agenda->markAttended($appointment);
        return redirect()->route('appointments.index')->with('status', 'Cita marcada como asistida');
    }

    public function noShow(Appointment $appointment): RedirectResponse
    {
        $this->agenda->markNoShow($appointment);
        return redirect()->route('appointments.index')->with('status', 'Cita marcada como no-show');
    }
}
