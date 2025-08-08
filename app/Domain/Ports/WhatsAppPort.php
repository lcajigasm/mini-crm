<?php

namespace App\Domain\Ports;

interface WhatsAppPort
{
    public function sendTemplate(string $to, string $templateName, array $variables = []): void;
}
