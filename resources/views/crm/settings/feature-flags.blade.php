@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4">Feature Flags</h1>

<div class="card">
  <div class="card-body">
    <p class="text-muted mb-3">Estos switches solo reflejan el estado actual desde la configuración. No guardan cambios todavía.</p>

    <div class="list-group">
      <label class="list-group-item d-flex justify-content-between align-items-center">
        <span>Telephony</span>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" type="checkbox" role="switch" disabled {{ \App\Support\Feature::enabled('TELEPHONY') ? 'checked' : '' }}>
        </div>
      </label>

      <label class="list-group-item d-flex justify-content-between align-items-center">
        <span>WhatsApp</span>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" type="checkbox" role="switch" disabled {{ \App\Support\Feature::enabled('WHATSAPP') ? 'checked' : '' }}>
        </div>
      </label>

      <label class="list-group-item d-flex justify-content-between align-items-center">
        <span>HubSpot</span>
        <div class="form-check form-switch m-0">
          <input class="form-check-input" type="checkbox" role="switch" disabled {{ \App\Support\Feature::enabled('HUBSPOT') ? 'checked' : '' }}>
        </div>
      </label>
    </div>
  </div>
</div>
@endsection
