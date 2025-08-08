<?php

namespace App\Domain\Ports;

interface CrmSyncPort
{
    public function upsertContact(array $contact): void;
    public function notifyAppointment(array $appointment): void;
}


