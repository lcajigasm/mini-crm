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
        <li class="nav-item"><a class="nav-link" href="{{ route('contacts.index') }}">Contactos</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('deals.index') }}">Deals</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('appointments.index') }}">Agenda</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('settings.feature-flags') }}">Ajustes</a></li>
      </ul>
    </div>
  </div>
</nav>
<div class="container-fluid">
  <div class="row">
    <aside class="col-12 col-md-3 col-lg-2 bg-light border-end min-vh-100 p-3">
      <div class="list-group list-group-flush">
        <a class="list-group-item list-group-item-action" href="{{ route('contacts.index') }}">Contactos</a>
        <a class="list-group-item list-group-item-action" href="{{ route('deals.index') }}">Pipelines</a>
        <a class="list-group-item list-group-item-action" href="{{ route('appointments.index') }}">Agenda</a>
      </div>
    </aside>
    <main class="col p-4">
      @yield('content')
    </main>
  </div>
</div>
</body>
</html>
