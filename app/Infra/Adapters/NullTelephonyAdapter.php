<?php

namespace App\Infra\Adapters;

use App\Domain\Ports\TelephonyPort;
use Illuminate\Support\Facades\Log;

class NullTelephonyAdapter implements TelephonyPort
{
    public function clickToCall(string $phoneNumber, array $context = []): void
    {
        Log::info('Click-to-call (noop)', ['phone' => $phoneNumber, 'context' => $context]);
    }
}
