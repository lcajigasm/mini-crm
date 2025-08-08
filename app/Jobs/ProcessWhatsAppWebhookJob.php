<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\WebhookEvent;
use App\Models\WhatsAppMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessWhatsAppWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public array $payload) {}

    public function handle(): void
    {
        $event = WebhookEvent::create([
            'provider' => 'whatsapp',
            'event_type' => $this->payload['event'] ?? 'message',
            'payload' => $this->payload,
            'status' => 'received',
        ]);

        try {
            $from = data_get($this->payload, 'from') ?? data_get($this->payload, 'entry.0.changes.0.value.messages.0.from');
            $text = data_get($this->payload, 'text') ?? data_get($this->payload, 'entry.0.changes.0.value.messages.0.text.body');
            $id = data_get($this->payload, 'id') ?? data_get($this->payload, 'entry.0.changes.0.value.messages.0.id');

            $customerId = null;
            $leadId = null;
            if ($from) {
                $customer = Customer::query()->where('phone', $from)->orWhere('secondary_phone', $from)->first();
                if ($customer) {
                    $customerId = $customer->id;
                } else {
                    $lead = Lead::query()->where('phone', $from)->first();
                    if ($lead) {
                        $leadId = $lead->id;
                    }
                }
            }

            WhatsAppMessage::create([
                'customer_id' => $customerId,
                'lead_id' => $leadId,
                'user_id' => null,
                'phone' => (string) $from,
                'direction' => 'inbound',
                'message' => (string) $text,
                'external_id' => (string) $id,
                'status' => 'received',
                'sent_at' => now(),
            ]);

            $event->update(['status' => 'processed', 'processed_at' => now()]);
        } catch (\Throwable $e) {
            Log::error('ProcessWhatsAppWebhookJob failed', ['error' => $e->getMessage()]);
            $event->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }
}


