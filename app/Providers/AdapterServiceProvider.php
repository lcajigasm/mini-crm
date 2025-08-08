<?php

namespace App\Providers;

use App\Domain\Ports\HubspotPort;
use App\Domain\Ports\TelephonyPort;
use App\Domain\Ports\WhatsAppPort;
use App\Infra\Adapters\NullHubspotAdapter;
use App\Infra\Adapters\NullTelephonyAdapter;
use App\Infra\Adapters\NullWhatsAppAdapter;
use App\Support\Feature;
use Illuminate\Support\ServiceProvider;

class AdapterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TelephonyPort::class, function () {
            if (Feature::enabled('TELEPHONY')) {
                // TODO: resolve concrete provider based on env('TELEPHONY_PROVIDER')
            }
            return new NullTelephonyAdapter();
        });

        $this->app->bind(WhatsAppPort::class, function () {
            if (Feature::enabled('WHATSAPP')) {
                // TODO: resolve concrete provider based on env('WHATSAPP_PROVIDER')
            }
            return new NullWhatsAppAdapter();
        });

        $this->app->bind(HubspotPort::class, function () {
            if (Feature::enabled('HUBSPOT')) {
                // TODO: return real HubSpot adapter
            }
            return new NullHubspotAdapter();
        });
    }
}
