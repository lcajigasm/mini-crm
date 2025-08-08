@extends('layouts.app')

@section('page-title', 'Clientes')

@section('content')
<div class="alert alert-info">Listado placeholder</div>

<ul class="list-group">
  @forelse($customers as $c)
    <li class="list-group-item d-flex justify-content-between align-items-center">
      <span>{{ $c->name }}</span>
      <small class="text-muted">{{ $c->email ?? $c->phone ?? '—' }}</small>
    </li>
  @empty
    <li class="list-group-item text-muted">Sin clientes</li>
  @endforelse
</ul>
@endsection
