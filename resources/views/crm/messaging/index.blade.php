@extends('layouts.app')

@section('page-title', 'Mensajería')

@section('content')
<div class="container">
  @if (session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  <div class="row">
    <div class="col-md-7">
      <h5>Plantillas</h5>
      <table class="table table-sm">
        <thead><tr><th>Nombre</th><th>Canal</th><th></th></tr></thead>
        <tbody>
        @foreach($templates as $tpl)
          <tr>
            <td>{{ $tpl->name }}</td>
            <td><span class="badge bg-secondary">{{ $tpl->channel }}</span></td>
            <td>
              <button class="btn btn-outline-primary btn-sm" onclick="showPreview({{ $tpl->id }})">Previsualizar</button>
            </td>
          </tr>
        @endforeach
        </tbody>
      </table>
    </div>
    <div class="col-md-5">
      <h5>Re-enviar manualmente</h5>
      <form method="post" id="sendForm">
        @csrf
        <div class="mb-2">
          <label class="form-label">Plantilla</label>
          <select class="form-select" id="templateSelect" name="template_id" onchange="onTplChange()">
            @foreach($templates as $tpl)
              <option value="{{ $tpl->id }}" data-url="{{ route('messaging.preview', $tpl) }}" data-send-url="{{ route('messaging.send', $tpl) }}">{{ $tpl->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="mb-2">
          <label class="form-label">Cliente demo</label>
          <select class="form-select" name="customer_id">
            <option value="">(sin asignar)</option>
            @foreach($customers as $c)
              <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->phone }})</option>
            @endforeach
          </select>
        </div>
        <div class="mb-2">
          <label class="form-label">Override teléfono</label>
          <input class="form-control" name="phone" placeholder="+349...">
        </div>
        <div class="mb-2">
          <label class="form-label">Override email</label>
          <input class="form-control" name="email" placeholder="demo@example.com">
        </div>
        <button class="btn btn-primary">Enviar</button>
      </form>
      <div class="mt-3">
        <h6>Previsualización</h6>
        <pre id="previewBox" style="white-space: pre-wrap"></pre>
      </div>
      <div class="mt-3">
        <h6>Últimos WhatsApp enviados</h6>
        <table class="table table-sm">
          <thead><tr><th>Fecha</th><th>Teléfono</th><th>Estado</th></tr></thead>
          <tbody>
          @foreach(($messages ?? []) as $m)
            <tr>
              <td>{{ $m->created_at->format('Y-m-d H:i') }}</td>
              <td>{{ $m->phone }}</td>
              <td><span class="badge text-bg-{{ $m->status === 'sent' ? 'success' : ($m->status === 'failed' ? 'danger':'secondary') }}">{{ $m->status ?? 'queued' }}</span></td>
            </tr>
          @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
function currentSendUrl(){
  const option = document.querySelector('#templateSelect').selectedOptions[0];
  return option.getAttribute('data-send-url');
}
function onTplChange(){
  const form = document.getElementById('sendForm');
  form.action = currentSendUrl();
  showPreview(document.getElementById('templateSelect').value);
}
async function showPreview(templateId){
  const option = document.querySelector('#templateSelect').selectedOptions[0];
  const url = option.getAttribute('data-url');
  const res = await fetch(url);
  const data = await res.json();
  document.getElementById('previewBox').innerText = data.content_text;
}
document.addEventListener('DOMContentLoaded', onTplChange);
</script>
@endsection



