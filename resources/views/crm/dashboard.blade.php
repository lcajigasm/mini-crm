@extends('layouts.app')

@section('page-title', 'Inicio')
@section('page-subtitle', 'Resumen operativo')

@section('content')
<div class="row g-3">
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">Agenda de hoy</h5>
        <p class="display-6 mb-0">{{ $todayAppointmentsCount ?? 0 }}</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">Leads nuevos (24h)</h5>
        <p class="display-6 mb-0">{{ $newLeads24hCount ?? 0 }}</p>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card h-100">
      <div class="card-body">
        <h5 class="card-title">No-show (semana)</h5>
        <p class="display-6 mb-0">{{ $noShowWeekCount ?? 0 }}</p>
      </div>
    </div>
  </div>
</div>
@endsection
