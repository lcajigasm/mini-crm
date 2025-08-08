<?php

namespace App\Infra\Adapters;

use App\Domain\Ports\WhatsAppPort;
use Illuminate\Support\Facades\Log;

class NullWhatsAppAdapter implements WhatsAppPort
{
    public function sendTemplate(string $to, string $templateName, array $variables = []): void
    {
        Log::info('WhatsApp template (noop)', compact('to', 'templateName', 'variables'));
    }
}
