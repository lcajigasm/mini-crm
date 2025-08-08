<?php

namespace App\Infra\Adapters;

use App\Domain\Ports\WhatsAppPort;
use Illuminate\Support\Facades\Log;

class MetaCloudAdapter implements WhatsAppPort
{
    public function sendTemplate(string $to, string $templateName, array $variables = []): void
    {
        Log::info('MetaCloud sendTemplate TODO', compact('to', 'templateName', 'variables'));
        // TODO: Implement WhatsApp Business Cloud API call
    }

    public function validateSignature(array $headers, string $payload): bool
    {
        Log::info('MetaCloud validateSignature TODO');
        // TODO: Validate using X-Hub-Signature-256 and app secret
        return true;
    }
}


