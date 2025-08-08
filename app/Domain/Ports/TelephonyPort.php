<?php

namespace App\Domain\Ports;

interface TelephonyPort
{
    public function clickToCall(string $phoneNumber, array $context = []): void;
    public function validateSignature(array $headers, string $payload): bool;
}
