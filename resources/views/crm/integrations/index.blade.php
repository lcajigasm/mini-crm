@extends('layouts.app')

@section('page-title', 'Integraciones')

@section('content')
<div class="container">
  <div class="row mb-4">
    <div class="col">
      <h2>Integraciones</h2>
      <p class="text-muted">Estado de conectores y últimos eventos procesados</p>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Telefonía</h5>
          <p>Estado: <span class="badge bg-{{ config('feature.TELEPHONY') ? 'success' : 'secondary' }}">{{ config('feature.TELEPHONY') ? 'ON' : 'OFF' }}</span></p>
          <p class="mb-0 small text-muted">Proveedor: {{ env('TELEPHONY_PROVIDER', 'null') }}</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">WhatsApp</h5>
          <p>Estado: <span class="badge bg-{{ config('feature.WHATSAPP') ? 'success' : 'secondary' }}">{{ config('feature.WHATSAPP') ? 'ON' : 'OFF' }}</span></p>
          <p class="mb-0 small text-muted">Proveedor: {{ env('WHATSAPP_PROVIDER', 'null') }}</p>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">HubSpot</h5>
          <p>Estado: <span class="badge bg-{{ config('feature.HUBSPOT') ? 'success' : 'secondary' }}">{{ config('feature.HUBSPOT') ? 'ON' : 'OFF' }}</span></p>
          <p class="mb-0 small text-muted">Proveedor: {{ env('CRM_SYNC_PROVIDER', 'hubspot') }}</p>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col">
      <h5>Últimos eventos</h5>
      <table class="table table-sm">
        <thead>
          <tr>
            <th>Fecha</th>
            <th>Proveedor</th>
            <th>Tipo</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          @foreach(\App\Models\WebhookEvent::query()->latest('received_at')->limit(15)->get() as $evt)
            <tr>
              <td>{{ $evt->received_at?->format('Y-m-d H:i:s') ?? $evt->created_at->format('Y-m-d H:i:s') }}</td>
              <td>{{ $evt->provider }}</td>
              <td>{{ $evt->event_type }}</td>
              <td>
                <span class="badge bg-{{ $evt->status === 'processed' ? 'success' : ($evt->status === 'failed' ? 'danger' : 'secondary') }}">{{ $evt->status }}</span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col">
      <h5>Métricas</h5>
      <ul>
        <li>WhatsApp mensajes totales: {{ \App\Models\WhatsAppMessage::query()->count() }}</li>
        <li>Llamadas totales: {{ \App\Models\CallLog::query()->count() }}</li>
        <li>Eventos de webhooks: {{ \App\Models\WebhookEvent::query()->count() }}</li>
      </ul>
    </div>
  </div>
</div>
@endsection



