<?php

namespace App\Domain\Ports;

interface HubspotPort
{
    public function syncContact(array $contact): void;
    public function syncDeal(array $deal): void;
    public function syncAppointment(array $appointment): void;
}
