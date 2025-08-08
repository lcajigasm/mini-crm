<?php

namespace App\Jobs;

use App\Models\CallLog;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\WebhookEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProcessInboundCallJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public array $payload) {}

    public function handle(): void
    {
        $event = WebhookEvent::create([
            'provider' => 'telephony',
            'event_type' => $this->payload['event'] ?? 'inbound_call',
            'payload' => $this->payload,
            'status' => 'received',
        ]);

        try {
            $from = $this->payload['from'] ?? $this->payload['caller'] ?? null;
            $normalized = $from ? $this->normalizePhone($from) : null;

            $customerId = null;
            $leadId = null;
            if ($normalized) {
                $customer = Customer::query()
                    ->where('phone', $normalized)
                    ->orWhere('secondary_phone', $normalized)
                    ->first();
                if ($customer) {
                    $customerId = $customer->id;
                } else {
                    $lead = Lead::query()->where('phone', $normalized)->first();
                    if ($lead) {
                        $leadId = $lead->id;
                    }
                }
            }

            CallLog::create([
                'customer_id' => $customerId,
                'lead_id' => $leadId,
                'user_id' => null,
                'phone' => $normalized ?? ($from ?? ''),
                'direction' => 'inbound',
                'status' => $this->payload['status'] ?? 'ringing',
                'duration_seconds' => (int) ($this->payload['duration'] ?? 0),
                'started_at' => now(),
                'notes' => $this->payload['note'] ?? null,
            ]);

            $event->update(['status' => 'processed', 'processed_at' => now()]);
        } catch (\Throwable $e) {
            Log::error('ProcessInboundCallJob failed', ['error' => $e->getMessage()]);
            $event->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }

    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/[^\d\+]/', '', $phone) ?? '';
        if (! Str::startsWith($digits, '+') && strlen($digits) === 10) {
            return '+34' . $digits; // default country fallback
        }
        return $digits;
    }
}


