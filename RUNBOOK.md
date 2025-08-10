## Incident runbooks

These guides are concise and actionable. Run commands from the project root on the server unless otherwise noted.

### 1) WhatsApp provider outage (Meta Cloud)
- Symptoms: failing webhooks, pending sends, high API latency.
- Check:
  - `tail -n 200 storage/logs/laravel.log | grep -i whatsapp | tail -n 50`
  - Queue: `php artisan queue:failed` and `php artisan queue:work --once`
  - Provider status page.
- Degrade/isolate:
  - Disable outgoing sends: `php artisan down --render="errors/maintenance"` for global impact or disable the feature flag if present.
  - Delay retries: pause workers `supervisorctl stop queue:*` (if applicable).
- Retry/recover:
  - Reprocess stored webhooks: `php artisan mini:replay-webhooks whatsapp` (if present) or re-dispatch jobs `php artisan queue:retry all`.
  - Review `storage/logs/laravel.log` and success metrics.

### 2) Telephony provider outage (Aircall or others)
- Symptoms: inbound not recorded, CTI down.
- Check:
  - `tail -n 200 storage/logs/laravel.log | grep -i call | tail -n 50`
  - Webhooks received in `webhook_events` (UI/DB if present).
- Degrade:
  - Route calls to a customer backup number.
  - Disable call UI if feature-flagged.
- Retry:
  - Reprocess events: `php artisan queue:retry all` after restoration.

### 3) HubSpot / external CRM outage
- Symptoms: failed sync, API 5xx, timeouts.
- Check:
  - `tail -n 200 storage/logs/laravel.log | grep -i hubspot | tail -n 50`
  - Provider status page.
- Degrade:
  - Switch sync to deferred (enqueue-only) while the provider is down.
  - Disable integrations from Settings if appropriate.
- Retry:
  - Re-trigger incremental sync (artisan command if present) or `queue:retry`.

### Health checks
- HTTP: `scripts/health_check.sh https://<host>/healthz`
- Quick DB: `php -r 'new PDO(getenv("DB_URL")?:"sqlite:database/database.sqlite"); echo "DB OK\n";'`

### Backups and log rotation
- Manual backup now: `scripts/backup_db.sh`
- Rotate logs: `scripts/rotate_logs.sh`

### Minimal post-mortem (within 24h)
- Timeline, root cause (if known), corrective actions, due date and owner.

## Runbook: Reports & KPIs

### Purpose
Reports dashboard with key KPIs and a JSON API for integrations.

### KPIs
- Leads in last 24h and 7d
- Appointment rate (leads 7d with appointment 7d / leads 7d)
- Attendance rate (attended / [attended + no-show + cancelled], measured by appointment date in 7d)
- Conversion to sale (leads 7d with treatment 7d / leads 7d)
- No-show ratio (same denominator as above)
- Completed sessions 6/6 (treatments that reached `sessions_count` with last session attended within 7d)

### Sources
Optional filter by lead `source`: `google`, `meta`, `organic`.

### Endpoints
- UI: Reports menu (see the application navigation)
- JSON API: `GET /api/reports/kpis?source=google` (requires authenticated session)

Example response (partial):

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
  "series_7d": [ {"date": "2025-01-01", "leads": 3 } ]
}
```

### Operations
1) Seed demo data: `php artisan migrate:fresh --seed`
2) Login with `admin@example.com` / `password`
3) View dashboard: Reports menu
4) Try the API: use the "JSON API" button on the page or `curl -b cookie ... /api/reports/kpis`

### Maintenance
- If lead `source` values change in `LeadFactory`, update the UI filter accordingly.
- Queries are optimized with aggregations and `pluck`/`groupBy`; add indexes as data grows.

### Deployment
- CI runs tests and build (`.github/workflows/ci.yml`).
- Minimal deploy: `bash scripts/deploy.sh /var/www/mini-crm php`.


