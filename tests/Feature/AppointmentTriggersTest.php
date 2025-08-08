<?php

use App\Domain\Services\AgendaService;
use App\Events\AppointmentCreated;
use App\Jobs\SendTemplateMessageJob;
use App\Models\Customer;
use App\Models\Template;
use Illuminate\Support\Facades\Bus;

it('dispatches confirmation job on appointment created', function () {
    Bus::fake();

    Template::factory()->create([
        'key' => 'appointment_confirmation',
        'channel' => 'whatsapp',
        'content_text' => 'Confirmación',
    ]);

    $customer = Customer::factory()->create();
    $agenda = app(AgendaService::class);
    $appt = $agenda->create([
        'customer_id' => $customer->id,
        'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
        'duration_minutes' => 30,
    ]);

    Bus::assertDispatched(SendTemplateMessageJob::class, function ($job) {
        return $job->templateKey === 'appointment_confirmation';
    });
});


