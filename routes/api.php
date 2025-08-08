<?php

use Illuminate\Support\Facades\Route;
use App\Jobs\LogTelephonyWebhook;
use App\Jobs\ProcessWhatsAppWebhook;
use App\Jobs\ProcessHubspotWebhook;

Route::post('/webhooks/telephony', function () {
    LogTelephonyWebhook::dispatch(request()->all());
    return response()->json(['ok' => true]);
});

Route::post('/webhooks/whatsapp', function () {
    ProcessWhatsAppWebhook::dispatch(request()->all());
    return response()->json(['ok' => true]);
});

Route::post('/webhooks/hubspot', function () {
    ProcessHubspotWebhook::dispatch(request()->all());
    return response()->json(['ok' => true]);
});
