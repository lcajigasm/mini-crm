<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Mini-CRM') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="{{ route('dashboard') }}">Mini-CRM</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        @php($user = auth()->user())
        @if($user && $user->hasRole(['reception','manager','admin']))
          <li class="nav-item"><a class="nav-link" href="{{ route('appointments.index') }}">Agenda</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('contacts.index') }}">Clientes</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('calls.index') }}">Llamadas</a></li>
        @endif
        @if($user && $user->hasRole(['manager','admin']))
          <li class="nav-item"><a class="nav-link" href="{{ route('leads.index') }}">Leads</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('messaging.index') }}">Mensajería</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('reports.index') }}">Informes</a></li>
        @endif
        @if($user && $user->isAdmin())
          <li class="nav-item"><a class="nav-link" href="{{ route('integrations.index') }}">Integraciones</a></li>
          <li class="nav-item"><a class="nav-link" href="{{ route('settings.index') }}">Ajustes</a></li>
        @endif
      </ul>
    </div>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">
    <aside class="col-12 col-md-3 col-lg-2 bg-light border-end min-vh-100 p-3">
      <div class="list-group list-group-flush">
        @php($user = auth()->user())
        @if($user && $user->hasRole(['reception','manager','admin']))
          <a class="list-group-item list-group-item-action" href="{{ route('appointments.index') }}">Agenda</a>
          <a class="list-group-item list-group-item-action" href="{{ route('contacts.index') }}">Clientes</a>
          <a class="list-group-item list-group-item-action" href="{{ route('calls.index') }}">Llamadas</a>
        @endif
        @if($user && $user->hasRole(['manager','admin']))
          <a class="list-group-item list-group-item-action" href="{{ route('leads.index') }}">Leads</a>
          <a class="list-group-item list-group-item-action" href="{{ route('messaging.index') }}">Mensajería</a>
          <a class="list-group-item list-group-item-action" href="{{ route('reports.index') }}">Informes</a>
        @endif
        @if($user && $user->isAdmin())
          <a class="list-group-item list-group-item-action" href="{{ route('integrations.index') }}">Integraciones</a>
          <a class="list-group-item list-group-item-action" href="{{ route('settings.index') }}">Ajustes</a>
        @endif
      </div>
    </aside>
    <main class="col p-4">
      <header class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h1 class="h4 mb-0">@yield('page-title', 'Mini-CRM')</h1>
          @hasSection('page-subtitle')
            <small class="text-muted">@yield('page-subtitle')</small>
          @endif
        </div>
        <div class="d-flex gap-2">
          @yield('page-actions')
        </div>
      </header>
      @yield('content')
      <footer class="border-top pt-3 mt-4 text-muted small">
        Cumplimiento GDPR · Seguridad de datos · Auditoría y trazabilidad de acciones
      </footer>
    </main>
  </div>
</div>
</body>
</html>
