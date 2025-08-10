<?php

use Illuminate\Support\Facades\Route;
use App\Jobs\ProcessInboundCallJob;
use App\Jobs\ProcessWhatsAppWebhookJob;
use App\Jobs\ProcessHubSpotWebhookJob;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\CRM\ReportsController;

Route::post('/webhooks/telephony', function () {
    ProcessInboundCallJob::dispatch(request()->all());
    return response()->json(['ok' => true]);
});

Route::post('/webhooks/whatsapp', function () {
    ProcessWhatsAppWebhookJob::dispatch(request()->all());
    return response()->json(['ok' => true]);
});

Route::post('/webhooks/hubspot', function () {
    ProcessHubSpotWebhookJob::dispatch(request()->all());
    return response()->json(['ok' => true]);
});

// Reports KPIs API (protected by session auth)
Route::middleware('auth')->get('/reports/kpis', [ReportsController::class, 'apiKpis']);
