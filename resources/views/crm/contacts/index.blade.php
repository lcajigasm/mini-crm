@extends('layouts.app')

@section('page-title', 'Clientes')

@section('content')
@if(session('status'))
  <div class="alert alert-success mb-3">{{ session('status') }}</div>
@endif

<ul class="list-group">
  @forelse($customers as $c)
    <li class="list-group-item d-flex justify-content-between align-items-center">
      <div>
        <div class="fw-semibold">{{ $c->name }}</div>
        <small class="text-muted">{{ $c->email ?? $c->phone ?? '—' }}</small>
      </div>
      <div class="btn-group btn-group-sm" role="group">
        <a href="{{ route('contacts.export', $c) }}" class="btn btn-outline-primary">Exportar</a>
        <form action="{{ route('contacts.erase', $c) }}" method="POST" onsubmit="return confirm('¿Anonimizar este cliente? Esta acción es irreversible.');">
          @csrf
          <button type="submit" class="btn btn-outline-danger">Anonimizar</button>
        </form>
      </div>
    </li>
  @empty
    <li class="list-group-item text-muted">Sin clientes</li>
  @endforelse
</ul>
@endsection
