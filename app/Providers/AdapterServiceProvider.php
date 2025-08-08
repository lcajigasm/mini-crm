<?php

namespace App\Providers;

use App\Domain\Ports\HubspotPort;
use App\Domain\Ports\CrmSyncPort;
use App\Domain\Ports\TelephonyPort;
use App\Domain\Ports\WhatsAppPort;
use App\Infra\Adapters\NullHubspotAdapter;
use App\Infra\Adapters\NullTelephonyAdapter;
use App\Infra\Adapters\NullWhatsAppAdapter;
use App\Infra\Adapters\AircallAdapter;
use App\Infra\Adapters\MetaCloudAdapter;
use App\Infra\Adapters\HubSpotAdapter as RealHubSpotCrmAdapter;
use App\Support\Feature;
use Illuminate\Support\ServiceProvider;

class AdapterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TelephonyPort::class, function () {
            if (Feature::enabled('TELEPHONY')) {
                $provider = strtolower((string) env('TELEPHONY_PROVIDER', ''));
                if ($provider === 'aircall') {
                    return new AircallAdapter();
                }
            }
            return new NullTelephonyAdapter();
        });

        $this->app->bind(WhatsAppPort::class, function () {
            if (Feature::enabled('WHATSAPP')) {
                $provider = strtolower((string) env('WHATSAPP_PROVIDER', ''));
                if ($provider === 'meta') {
                    return new MetaCloudAdapter();
                }
            }
            return new NullWhatsAppAdapter();
        });

        $this->app->bind(HubspotPort::class, function () {
            if (Feature::enabled('HUBSPOT')) {
                // TODO: return real HubSpot adapter
            }
            return new NullHubspotAdapter();
        });

        // New CRM sync port (preferred)
        $this->app->bind(CrmSyncPort::class, function () {
            if (Feature::enabled('HUBSPOT')) {
                $provider = strtolower((string) env('CRM_SYNC_PROVIDER', 'hubspot'));
                if ($provider === 'hubspot') {
                    return new RealHubSpotCrmAdapter();
                }
            }
            // Fallback to noop via the legacy null adapter wrapped in a simple bridge
            return new class implements CrmSyncPort {
                public function upsertContact(array $contact): void {}
                public function notifyAppointment(array $appointment): void {}
            };
        });
    }
}
