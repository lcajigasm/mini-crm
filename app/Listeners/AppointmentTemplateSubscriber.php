<?php

namespace App\Listeners;

use App\Events\AppointmentAttended;
use App\Events\AppointmentCancelled;
use App\Events\AppointmentCreated;
use App\Events\AppointmentNoShow;
use App\Events\AppointmentRescheduled;
use App\Jobs\SendTemplateMessageJob;

class AppointmentTemplateSubscriber
{
    public function handleCreated(AppointmentCreated $event): void
    {
        $appointment = $event->appointment;
        SendTemplateMessageJob::dispatch(
            'appointment_confirmation',
            $appointment->customer_id,
            $appointment->lead_id,
            self::vars($appointment)
        );
    }

    public function handleRescheduled(AppointmentRescheduled $event): void
    {
        $appointment = $event->appointment;
        SendTemplateMessageJob::dispatch(
            'appointment_rescheduled',
            $appointment->customer_id,
            $appointment->lead_id,
            self::vars($appointment)
        );
    }

    public function handleCancelled(AppointmentCancelled $event): void
    {
        $appointment = $event->appointment;
        SendTemplateMessageJob::dispatch(
            'appointment_cancelled',
            $appointment->customer_id,
            $appointment->lead_id,
            self::vars($appointment)
        );
    }

    public function handleAttended(AppointmentAttended $event): void
    {
        $appointment = $event->appointment;
        SendTemplateMessageJob::dispatch(
            'appointment_post_visit',
            $appointment->customer_id,
            $appointment->lead_id,
            self::vars($appointment)
        );
    }

    public function handleNoShow(AppointmentNoShow $event): void
    {
        $appointment = $event->appointment;
        SendTemplateMessageJob::dispatch(
            'appointment_no_show',
            $appointment->customer_id,
            $appointment->lead_id,
            self::vars($appointment)
        );
    }

    public function subscribe($events): void
    {
        $events->listen(AppointmentCreated::class, [self::class, 'handleCreated']);
        $events->listen(AppointmentRescheduled::class, [self::class, 'handleRescheduled']);
        $events->listen(AppointmentCancelled::class, [self::class, 'handleCancelled']);
        $events->listen(AppointmentAttended::class, [self::class, 'handleAttended']);
        $events->listen(AppointmentNoShow::class, [self::class, 'handleNoShow']);
    }

    private static function vars($appointment): array
    {
        $customerName = $appointment->customer->name ?? '';
        return [
            'appointment' => [
                'date' => optional($appointment->scheduled_at)->format('d/m/Y H:i') ?? '',
                'location' => $appointment->location ?? '',
                'session_number' => $appointment->session_number ?? '',
            ],
            'customer' => [
                'name' => $customerName,
            ],
        ];
    }
}


