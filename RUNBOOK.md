## Runbooks de incidentes

Estas guías son breves y accionables. Ejecuta comandos desde la raíz del proyecto en el servidor a menos que se indique lo contrario.

### 1) Caída proveedor WhatsApp (Meta Cloud)
- **Síntomas**: Webhooks fallan, envíos pendientes, alta latencia API.
- **Comprobar**:
  - `tail -n 200 storage/logs/laravel.log | grep -i whatsapp | tail -n 50`
  - Cola: `php artisan queue:failed` y `php artisan queue:work --once`
  - Estado proveedor: página de status del proveedor.
- **Degradar/aislar**:
  - Desactivar envíos salientes: `php artisan down --render="errors/maintenance"` si impacto global o deshabilita feature flag si existe.
  - Reintentos diferidos: pausar workers `supervisorctl stop queue:*` (si aplica).
- **Reintentar/recuperar**:
  - Reprocesar webhooks almacenados: `php artisan mini:replay-webhooks whatsapp` (si existe) o re-disparar jobs `php artisan queue:retry all`.
  - Revisar `storage/logs/laravel.log` y métricas de éxito.

### 2) Caída proveedor Telefonía (Aircall u otro)
- **Síntomas**: Entrantes no registradas, CTI fuera de servicio.
- **Comprobar**:
  - `tail -n 200 storage/logs/laravel.log | grep -i call | tail -n 50`
  - Webhooks recibidos en `webhook_events` (si hay panel/DB).
- **Degradar**:
  - Enrutar llamadas a número de respaldo del cliente.
  - Desactivar UI de llamadas si hay feature flag.
- **Reintentar**:
  - Reprocesar eventos: `php artisan queue:retry all` tras restablecimiento.

### 3) Caída HubSpot / CRM externo
- **Síntomas**: Sync fallido, API 5xx, timeouts.
- **Comprobar**:
  - `tail -n 200 storage/logs/laravel.log | grep -i hubspot | tail -n 50`
  - Estado proveedor.
- **Degradar**:
  - Convertir sincronización a diferida (solo encolado) mientras el proveedor está caído.
  - Deshabilitar integraciones desde Ajustes si procede.
- **Reintentar**:
  - Re-disparar sincronización incremental (comando `artisan` si existe) o `queue:retry`.

### Health checks
- HTTP: `scripts/health_check.sh https://<host>/healthz`
- DB rápido: `php -r 'new PDO(getenv("DB_URL")?:"sqlite:database/database.sqlite"); echo "DB OK\n";'`

### Copias de seguridad y rotación de logs
- Backup manual inmediato: `scripts/backup_db.sh`
- Rotar logs: `scripts/rotate_logs.sh`

### Post-mortem mínimo (24h)
- Línea temporal, causa raíz (si se conoce), acciones correctivas, due date y owner.

## Runbook: Informes y KPIs

### Propósito
Panel de informes con KPIs clave y API JSON para integraciones.

### KPIs
- Leads en 24h y 7d
- Tasa de cita (leads 7d con cita creada 7d / leads 7d)
- Tasa de asistencia (asistidas / [asistidas + no-show + canceladas], en 7d por fecha de cita)
- Conversión a venta (leads 7d con tratamiento 7d / leads 7d)
- No-show (ratio en mismo denominador anterior)
- Sesiones completadas 6/6 (treatments que alcanzan `sessions_count` con la última sesión asistida en 7d)

### Fuentes
Filtro opcional por `source` del lead: `google`, `meta`, `organic`.

### Endpoints
- UI: `GET /informes` (menú Informes)
- API JSON: `GET /api/reports/kpis?source=google` (requiere sesión autenticada)

Respuesta ejemplo (parcial):

```json
{
  "filters": { "source": "google" },
  "totals": {
    "leads_24h": 4,
    "leads_7d": 12,
    "appointment_rate_7d": 0.5,
    "attendance_rate_7d": 0.67,
    "conversion_rate_7d": 0.25,
    "no_show_rate_7d": 0.17,
    "sessions_completed_7d": 2
  },
  "series_7d": [ {"date": "2025-01-01", "leads": 3, ...} ]
}
```

### Operativa
1) Semillas demo: `php artisan migrate:fresh --seed`
2) Acceder con `admin@example.com` / `password`
3) Ver panel: menú Informes
4) Probar API: abrir botón "API JSON" en la página o `curl -b cookie ... /api/reports/kpis`

### Mantenimiento
- Si cambian nombres de `source` en `LeadFactory`, ajustar el filtro de la UI.
- Consultas están optimizadas con agregaciones y `pluck`/`groupBy`; añadir índices si los datos crecen.

### Despliegue
- CI ejecuta tests y build (`.github/workflows/ci.yml`).
- Despliegue mínimo: `bash scripts/deploy.sh /var/www/mini-crm php`.


