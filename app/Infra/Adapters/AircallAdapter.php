<?php

namespace App\Infra\Adapters;

use App\Domain\Ports\TelephonyPort;
use Illuminate\Support\Facades\Log;

class AircallAdapter implements TelephonyPort
{
    public function clickToCall(string $phoneNumber, array $context = []): void
    {
        Log::info('Aircall clickToCall TODO', ['phone' => $phoneNumber, 'context' => $context]);
        // TODO: Implement API call to Aircall
    }

    public function validateSignature(array $headers, string $payload): bool
    {
        Log::info('Aircall validateSignature TODO');
        // TODO: Validate using Aircall signature header and webhook secret
        return true;
    }
}


