<?php

namespace App\Domain\Services;

use App\Events\AppointmentAttended;
use App\Events\AppointmentCancelled;
use App\Events\AppointmentCreated;
use App\Events\AppointmentNoShow;
use App\Events\AppointmentRescheduled;
use App\Models\Appointment;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AgendaService
{
    public function create(array $data): Appointment
    {
        $validated = $this->validateBase($data);

        $this->assertNoOverlap(null, $validated['customer_id'] ?? null, $validated['lead_id'] ?? null, $validated['scheduled_at'], $validated['duration_minutes']);

        $appointment = Appointment::create([
            'customer_id' => $validated['customer_id'] ?? null,
            'lead_id' => $validated['lead_id'] ?? null,
            'treatment_id' => $validated['treatment_id'] ?? null,
            'scheduled_at' => $validated['scheduled_at'],
            'duration_minutes' => $validated['duration_minutes'],
            'status' => 'scheduled',
            'session_number' => $validated['session_number'] ?? null,
            'location' => $validated['location'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        AppointmentCreated::dispatch($appointment);

        return $appointment;
    }

    public function reschedule(Appointment $appointment, CarbonInterface $newDateTime, int $durationMinutes): Appointment
    {
        $this->assertDuration($durationMinutes);

        $this->assertNoOverlap($appointment->id, $appointment->customer_id, $appointment->lead_id, $newDateTime, $durationMinutes);

        $appointment->scheduled_at = $newDateTime;
        $appointment->duration_minutes = $durationMinutes;
        $appointment->status = 'rescheduled';
        $appointment->save();

        AppointmentRescheduled::dispatch($appointment);

        return $appointment;
    }

    public function cancel(Appointment $appointment): Appointment
    {
        $appointment->status = 'cancelled';
        $appointment->save();

        AppointmentCancelled::dispatch($appointment);

        return $appointment;
    }

    public function markAttended(Appointment $appointment): Appointment
    {
        $appointment->status = 'attended';
        $appointment->save();

        AppointmentAttended::dispatch($appointment);

        return $appointment;
    }

    public function markNoShow(Appointment $appointment): Appointment
    {
        $appointment->status = 'no_show';
        $appointment->save();

        AppointmentNoShow::dispatch($appointment);

        return $appointment;
    }

    private function validateBase(array $data): array
    {
        $validator = Validator::make($data, [
            'customer_id' => ['nullable', 'integer', 'exists:customers,id', 'required_without:lead_id'],
            'lead_id' => ['nullable', 'integer', 'exists:leads,id', 'required_without:customer_id'],
            'treatment_id' => ['nullable', 'integer', 'exists:treatments,id'],
            'scheduled_at' => ['required', 'date'],
            'duration_minutes' => ['nullable', 'integer', 'min:5', 'max:480'],
            'session_number' => ['nullable', 'integer', 'min:1'],
            'location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();
        $validated['duration_minutes'] = $validated['duration_minutes'] ?? 30;
        $validated['scheduled_at'] = \Carbon\Carbon::parse($validated['scheduled_at']);

        $this->assertDuration($validated['duration_minutes']);

        return $validated;
    }

    private function assertDuration(int $durationMinutes): void
    {
        if ($durationMinutes < 5 || $durationMinutes > 480) {
            throw ValidationException::withMessages([
                'duration_minutes' => 'La duración debe estar entre 5 y 480 minutos.',
            ]);
        }
    }

    private function assertNoOverlap(?int $ignoreId, ?int $customerId, ?int $leadId, CarbonInterface $start, int $durationMinutes): void
    {
        $end = $start->clone()->addMinutes($durationMinutes);

        $query = Appointment::query()
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->when($customerId, fn ($q) => $q->where('customer_id', $customerId))
            ->when(!$customerId && $leadId, fn ($q) => $q->where('lead_id', $leadId))
            ->whereNotIn('status', ['cancelled'])
            ->where(function ($q) use ($start, $end) {
                $driver = DB::connection()->getDriverName();
                $q->where('scheduled_at', '<', $end);

                if ($driver === 'mysql') {
                    $q->whereRaw('DATE_ADD(scheduled_at, INTERVAL duration_minutes MINUTE) > ?', [$start->toDateTimeString()]);
                } elseif ($driver === 'sqlite') {
                    $q->whereRaw("datetime(scheduled_at, '+' || duration_minutes || ' minutes') > ?", [$start->toDateTimeString()]);
                } elseif ($driver === 'pgsql') {
                    $q->whereRaw("(scheduled_at + (duration_minutes || ' minutes')::interval) > ?", [$start->toDateTimeString()]);
                } else {
                    // Fallback: conservatively return possible overlap by ignoring duration-based filter
                    $q->where('scheduled_at', '>=', $start->clone()->subHours(12));
                }
            });

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'scheduled_at' => 'Existe una cita solapada en ese horario.',
            ]);
        }
    }
}


