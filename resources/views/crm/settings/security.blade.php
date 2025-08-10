@extends('layouts.app')

@section('page-title', 'Checklist de seguridad')

@section('content')
<div class="card">
  <div class="card-body">
    <h5 class="card-title">Básicos</h5>
    <ul>
      <li>Contraseñas: reglas de validación reforzadas (min longitud, mezcla, leak check opcional).</li>
      <li>CSRF: formularios Blade con <code>@csrf</code>.</li>
      <li>Sesiones: <code>SameSite</code> y <code>secure</code> en producción; expiración razonable.</li>
      <li>2FA (opcional): habilitar con Breeze/Jetstream si procede.</li>
      <li>Logs: auditoría activada para acciones críticas (export/erase/consent).</li>
    </ul>
    <h5 class="card-title mt-3">Siguientes pasos</h5>
    <ul>
      <li>Rate limiting en endpoints sensibles.</li>
      <li>Cifrado de columnas con PII si se requiere.</li>
      <li>Backups cifrados y política de retención.</li>
    </ul>
  </div>
</div>
@endsection


