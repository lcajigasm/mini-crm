@extends('layouts.app')

@section('page-title', 'Informes')

@section('content')
<div class="d-flex align-items-center gap-3 mb-3">
  <form method="get" class="d-flex align-items-center gap-2">
    <label for="source" class="form-label mb-0">Fuente</label>
    <select id="source" name="source" class="form-select form-select-sm" onchange="this.form.submit()">
      <option value="">Todas</option>
      @foreach($sources as $s)
        <option value="{{ $s }}" @selected(($data['filters']['source'] ?? '') === $s)>{{ ucfirst($s) }}</option>
      @endforeach
    </select>
  </form>
  <a class="btn btn-sm btn-outline-secondary" href="{{ route('reports.index') }}">Reset</a>
  <a class="btn btn-sm btn-outline-primary" href="{{ url('/api/reports/kpis' . (request('source') ? ('?source=' . request('source')) : '')) }}" target="_blank">API JSON</a>
  <span class="text-muted small">Últimos 7 días</span>
  <span class="ms-auto text-muted small">Actualizado: {{ now()->format('Y-m-d H:i') }}</span>
  </div>

<div class="row g-3">
  <div class="col-6 col-md-3">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <div class="text-muted small">Leads 24h</div>
        <div class="display-6">{{ $data['totals']['leads_24h'] }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <div class="text-muted small">Leads 7d</div>
        <div class="display-6">{{ $data['totals']['leads_7d'] }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <div class="text-muted small">Tasa de cita (7d)</div>
        <div class="display-6">{{ number_format($data['totals']['appointment_rate_7d'] * 100, 1) }}%</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <div class="text-muted small">Tasa asistencia (7d)</div>
        <div class="display-6">{{ number_format($data['totals']['attendance_rate_7d'] * 100, 1) }}%</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <div class="text-muted small">Conversión a venta (7d)</div>
        <div class="display-6">{{ number_format($data['totals']['conversion_rate_7d'] * 100, 1) }}%</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <div class="text-muted small">No-show (7d)</div>
        <div class="display-6">{{ number_format($data['totals']['no_show_rate_7d'] * 100, 1) }}%</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card shadow-sm h-100">
      <div class="card-body">
        <div class="text-muted small">Sesiones completadas 6/6 (7d)</div>
        <div class="display-6">{{ $data['totals']['sessions_completed_7d'] }}</div>
      </div>
    </div>
  </div>
</div>

<div class="card mt-4">
  <div class="card-header">Serie diaria (7d)</div>
  <div class="table-responsive">
    <table class="table table-sm mb-0 align-middle">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Leads</th>
          <th>Citas creadas</th>
          <th>Asistidas</th>
          <th>No-show</th>
          <th>Canceladas</th>
          <th>Tasa asistencia</th>
          <th>Conversión</th>
          <th>Sesiones 6/6</th>
        </tr>
      </thead>
      <tbody>
        @foreach($data['series_7d'] as $row)
          <tr>
            <td>{{ $row['date'] }}</td>
            <td>{{ $row['leads'] }}</td>
            <td>{{ $row['appointments_created'] }}</td>
            <td>{{ $row['attended'] }}</td>
            <td>{{ $row['no_show'] }}</td>
            <td>{{ $row['cancelled'] }}</td>
            <td>{{ number_format($row['attendance_rate'] * 100, 0) }}%</td>
            <td>{{ $row['conversions'] }}</td>
            <td>{{ $row['sessions_completed'] }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection




