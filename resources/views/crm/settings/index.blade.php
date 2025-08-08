@extends('layouts.app')

@section('page-title', 'Ajustes')

@section('content')
<div class="list-group">
  <a href="{{ route('settings.feature-flags') }}" class="list-group-item list-group-item-action">Feature Flags</a>
  <div class="list-group-item text-muted">Otros ajustes (placeholder)</div>
  
</div>
@endsection



