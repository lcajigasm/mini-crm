<?php

use App\Domain\Services\AgendaService;
use App\Events\AppointmentAttended;
use App\Events\AppointmentCancelled;
use App\Events\AppointmentCreated;
use App\Events\AppointmentNoShow;
use App\Events\AppointmentRescheduled;
use App\Models\Appointment;
use App\Models\Customer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    Event::fake();
});

it('crea una cita válida y dispara evento', function () {
    $service = app(AgendaService::class);
    $customer = Customer::factory()->create();
    $dt = Carbon::parse('2025-01-01 10:00:00');

    $appointment = $service->create([
        'customer_id' => $customer->id,
        'scheduled_at' => $dt->toDateTimeString(),
        'duration_minutes' => 30,
    ]);

    expect($appointment)->toBeInstanceOf(Appointment::class)
        ->and($appointment->status)->toBe('scheduled')
        ->and($appointment->duration_minutes)->toBe(30);

    Event::assertDispatched(AppointmentCreated::class);
});

it('evita solapes simples por cliente', function () {
    $service = app(AgendaService::class);
    $customer = Customer::factory()->create();

    $service->create([
        'customer_id' => $customer->id,
        'scheduled_at' => '2025-01-01 10:00:00',
        'duration_minutes' => 60,
    ]);

    $service->create([
        'customer_id' => $customer->id,
        'scheduled_at' => '2025-01-01 11:00:00',
        'duration_minutes' => 30,
    ]);

    $this->expectException(\Illuminate\Validation\ValidationException::class);
    $service->create([
        'customer_id' => $customer->id,
        'scheduled_at' => '2025-01-01 10:30:00',
        'duration_minutes' => 30,
    ]);
});

it('reprograma y dispara evento', function () {
    $service = app(AgendaService::class);
    $customer = Customer::factory()->create();
    $appointment = $service->create([
        'customer_id' => $customer->id,
        'scheduled_at' => '2025-01-01 10:00:00',
        'duration_minutes' => 30,
    ]);

    $service->reschedule($appointment, Carbon::parse('2025-01-01 12:00:00'), 45);

    expect($appointment->fresh()->status)->toBe('rescheduled')
        ->and($appointment->duration_minutes)->toBe(45)
        ->and($appointment->scheduled_at->format('H:i'))->toBe('12:00');

    Event::assertDispatched(AppointmentRescheduled::class);
});

it('cancela y marca attended / no-show', function () {
    $service = app(AgendaService::class);
    $customer = Customer::factory()->create();
    $appointment = $service->create([
        'customer_id' => $customer->id,
        'scheduled_at' => '2025-01-01 10:00:00',
        'duration_minutes' => 30,
    ]);

    $service->cancel($appointment);
    expect($appointment->fresh()->status)->toBe('cancelled');
    Event::assertDispatched(AppointmentCancelled::class);

    $service->markAttended($appointment);
    expect($appointment->fresh()->status)->toBe('attended');
    Event::assertDispatched(AppointmentAttended::class);

    $service->markNoShow($appointment);
    expect($appointment->fresh()->status)->toBe('no_show');
    Event::assertDispatched(AppointmentNoShow::class);
});


