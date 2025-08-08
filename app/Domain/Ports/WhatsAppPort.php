<?php

namespace App\Domain\Ports;

interface WhatsAppPort
{
    public function sendTemplate(string $to, string $templateName, array $variables = []): void;
    public function validateSignature(array $headers, string $payload): bool;
}
