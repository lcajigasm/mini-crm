@extends('layouts.app')

@section('content')
<h1 class="h3 mb-4">Feature Flags</h1>
<ul class="list-group">
  <li class="list-group-item d-flex justify-content-between align-items-center">TELEPHONY <span class="badge bg-{{ \App\Support\Feature::enabled('TELEPHONY') ? 'success' : 'secondary' }}">{{ \App\Support\Feature::enabled('TELEPHONY') ? 'ON' : 'OFF' }}</span></li>
  <li class="list-group-item d-flex justify-content-between align-items-center">WHATSAPP <span class="badge bg-{{ \App\Support\Feature::enabled('WHATSAPP') ? 'success' : 'secondary' }}">{{ \App\Support\Feature::enabled('WHATSAPP') ? 'ON' : 'OFF' }}</span></li>
  <li class="list-group-item d-flex justify-content-between align-items-center">HUBSPOT <span class="badge bg-{{ \App\Support\Feature::enabled('HUBSPOT') ? 'success' : 'secondary' }}">{{ \App\Support\Feature::enabled('HUBSPOT') ? 'ON' : 'OFF' }}</span></li>
</ul>
@endsection
