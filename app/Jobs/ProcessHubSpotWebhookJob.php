<?php

namespace App\Jobs;

use App\Models\WebhookEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessHubSpotWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public array $payload) {}

    public function handle(): void
    {
        $event = WebhookEvent::create([
            'provider' => 'hubspot',
            'event_type' => $this->payload['event'] ?? 'event',
            'payload' => $this->payload,
            'status' => 'received',
        ]);

        try {
            // For now just log; future: route to CRM sync flows
            Log::info('HubSpot webhook received', $this->payload);
            $event->update(['status' => 'processed', 'processed_at' => now()]);
        } catch (\Throwable $e) {
            Log::error('ProcessHubSpotWebhookJob failed', ['error' => $e->getMessage()]);
            $event->update(['status' => 'failed', 'error_message' => $e->getMessage()]);
        }
    }
}


