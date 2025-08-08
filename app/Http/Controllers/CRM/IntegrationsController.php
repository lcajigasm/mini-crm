<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Models\WebhookEvent;
use App\Models\WhatsAppMessage;
use Illuminate\Support\Facades\Cache;

class IntegrationsController extends Controller
{
    public function index()
    {
        // Provide quick counters via cache for speed if needed later
        $metrics = [
            'whatsapp_total' => WhatsAppMessage::query()->count(),
            'calls_total' => CallLog::query()->count(),
            'webhooks_total' => WebhookEvent::query()->count(),
        ];
        return view('crm.integrations.index', compact('metrics'));
    }
}



