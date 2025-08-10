@extends('layouts.app')

@section('page-title', 'Auditoría')

@section('content')
<div class="card">
  <div class="card-header">Últimas acciones</div>
  <div class="table-responsive">
    <table class="table table-striped mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Fecha</th>
          <th>Usuario</th>
          <th>Acción</th>
          <th>Target</th>
          <th>IP</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
          <tr>
            <td>{{ $log->id }}</td>
            <td>{{ $log->created_at }}</td>
            <td>{{ $log->user_id ?? '—' }}</td>
            <td><code>{{ $log->action }}</code></td>
            <td>{{ class_basename($log->target_type) }}#{{ $log->target_id }}</td>
            <td>{{ $log->ip_address ?? '—' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-muted">Sin registros</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="card-body small text-muted">Se muestran las últimas 50 acciones.</div>
  @endsection


