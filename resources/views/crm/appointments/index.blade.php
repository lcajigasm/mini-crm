@extends('layouts.app')

@section('page-title', 'Agenda')

@section('page-actions')
<button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#newAppt" aria-expanded="false">Nueva cita</button>
@endsection

@section('content')
<div class="alert alert-info">Agenda placeholder: crear, reprogramar, cancelar, asistir, no-show</div>

<div id="newAppt" class="collapse mb-3">
  <form method="POST" action="{{ route('appointments.store') }}" class="row g-2 align-items-end">
    @csrf
    <div class="col-auto">
      <label class="form-label">Cliente ID</label>
      <input type="number" name="customer_id" class="form-control" placeholder="ID cliente">
    </div>
    <div class="col-auto">
      <label class="form-label">Lead ID</label>
      <input type="number" name="lead_id" class="form-control" placeholder="ID lead">
    </div>
    <div class="col-auto">
      <label class="form-label">Fecha/Hora</label>
      <input type="datetime-local" name="scheduled_at" class="form-control" required>
    </div>
    <div class="col-auto">
      <label class="form-label">Duración (min)</label>
      <input type="number" name="duration_minutes" class="form-control" value="30" min="5" max="480">
    </div>
    <div class="col-auto">
      <button class="btn btn-success">Crear</button>
    </div>
  </form>
  @if ($errors->any())
    <div class="alert alert-danger mt-2">
      <ul class="mb-0">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  @if (session('status'))
    <div class="alert alert-success mt-2">{{ session('status') }}</div>
  @endif
</div>

<div class="table-responsive">
  <table class="table table-sm">
    <thead>
      <tr>
        <th>Fecha/Hora</th>
        <th>Cliente</th>
        <th>Estado</th>
        <th>Sesión</th>
        <th>Duración</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      @forelse($appointments as $appt)
        <tr>
          <td>{{ $appt->scheduled_at->format('d/m/Y H:i') }}</td>
          <td>{{ $appt->customer->name ?? '—' }}</td>
          <td><span class="badge text-bg-secondary">{{ $appt->status }}</span></td>
          <td>{{ $appt->session_number ?? '—' }}</td>
          <td>{{ $appt->duration_minutes }}m</td>
          <td>
            <div class="d-flex gap-1">
              <form method="POST" action="{{ route('appointments.reschedule', $appt) }}">
                @csrf
                <input type="datetime-local" name="scheduled_at" class="form-control form-control-sm" style="width: 210px" required>
                <input type="number" name="duration_minutes" class="form-control form-control-sm" style="width: 120px" value="{{ $appt->duration_minutes }}" min="5" max="480">
                <button class="btn btn-sm btn-outline-primary mt-1">Reprogramar</button>
              </form>
              <form method="POST" action="{{ route('appointments.cancel', $appt) }}">
                @csrf
                <button class="btn btn-sm btn-outline-danger">Cancelar</button>
              </form>
              <form method="POST" action="{{ route('appointments.attend', $appt) }}">
                @csrf
                <button class="btn btn-sm btn-outline-success">Asistió</button>
              </form>
              <form method="POST" action="{{ route('appointments.no_show', $appt) }}">
                @csrf
                <button class="btn btn-sm btn-outline-secondary">No-show</button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr><td colspan="4" class="text-muted">Sin citas</td></tr>
      @endforelse
    </tbody>
  </table>
  </div>
@endsection
