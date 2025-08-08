<?php

namespace App\Infra\Adapters;

use App\Domain\Ports\CrmSyncPort;
use Illuminate\Support\Facades\Log;

class HubSpotAdapter implements CrmSyncPort
{
    public function upsertContact(array $contact): void
    {
        Log::info('HubSpot upsertContact TODO', $contact);
        // TODO: Implement HubSpot CRM contact upsert
    }

    public function notifyAppointment(array $appointment): void
    {
        Log::info('HubSpot notifyAppointment TODO', $appointment);
        // TODO: Implement HubSpot engagement or custom object sync
    }
}


