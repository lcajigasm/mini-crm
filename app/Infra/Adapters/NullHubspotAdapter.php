<?php

namespace App\Infra\Adapters;

use App\Domain\Ports\HubspotPort;
use Illuminate\Support\Facades\Log;

class NullHubspotAdapter implements HubspotPort
{
    public function syncContact(array $contact): void
    {
        Log::info('HubSpot sync contact (noop)', $contact);
    }

    public function syncDeal(array $deal): void
    {
        Log::info('HubSpot sync deal (noop)', $deal);
    }

    public function syncAppointment(array $appointment): void
    {
        Log::info('HubSpot sync appointment (noop)', $appointment);
    }
}
