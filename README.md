## Mini-CRM

Open-source mini CRM built with Laravel 11, Breeze (Blade), Bootstrap + Vite, and Pest. Ideal for demos, learning, and small projects.

### Features

- Customers, leads, pipelines and stages
- Appointments with attendance tracking (attended / no-show / cancelled)
- Call logs and basic telephony/webhook ingestion
- WhatsApp and email message models with queue-based processing
- Reports & KPIs (UI and authenticated JSON API)
- Templates and audit logs
- Basic GDPR tooling (consent, export, erase/anonymize)
- Pluggable adapters for HubSpot, telephony, and WhatsApp providers

### Requirements

- PHP 8.2+
- Composer 2.x
- Node.js 20+ and npm 10+
- SQLite3

### Quick start (cloned repo)

1) Install dependencies, configure SQLite in `.env`, run migrations and seed:

```bash
cp .env.example .env
composer install
npm ci
[ -f database/database.sqlite ] || touch database/database.sqlite
sed -i '' \
  -e 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' \
  -e '/^DB_HOST=/ s/^/# /' \
  -e '/^DB_PORT=/ s/^/# /' \
  -e '/^DB_DATABASE=/ s/^/# /' \
  -e '/^DB_USERNAME=/ s/^/# /' \
  -e '/^DB_PASSWORD=/ s/^/# /' .env
php artisan key:generate
php artisan migrate:fresh --seed
```

2) Start dev servers (in two terminals):

```bash
npm run dev
php artisan serve
```

Open `http://127.0.0.1:8000`.

### Demo credentials (seed)

- Email: `admin@example.com`
- Password: `password`

### Horizon (optional)

```bash
# Requires Redis (phpredis extension or predis package). Example with predis:
composer require laravel/horizon predis/predis
php artisan horizon:install
php artisan vendor:publish --tag=horizon-config
# Update .env: QUEUE_CONNECTION=redis and REDIS_CLIENT=predis
php artisan migrate
php artisan horizon
```

### Common commands

- Vite (dev): `npm run dev`
- Vite (build): `npm run build`
- Laravel server: `php artisan serve`
- Tests (Pest): `php artisan test` or `./vendor/bin/pest`
- Re-seed quickly: `php artisan migrate:fresh --seed`

### Reports & KPIs

- UI: menu `Reports` → KPI cards + table.
- Optional filter by lead `source` (if present): `all`, `google`, `meta`, `organic`.
- Authenticated JSON API: `GET /api/reports/kpis?source=google`.

Seed data includes a mix of sources, attended/no-show appointments, and completed treatments so the dashboard feels "alive".

### CI and Deployment

- CI (GitHub Actions): `.github/workflows/ci.yml` installs Composer, runs `vendor/bin/pest`, and `npm run build` on every PR and push to `main`.
- Deploy scripts:
  - Generic: `bash scripts/deploy.sh /var/www/mini-crm php`
  - Staging: `HEALTHCHECK_URL="https://staging.your-domain/healthz" bash scripts/deploy-staging.sh`
  - Production: `HEALTHCHECK_URL="https://mini-crm.your-domain/healthz" bash scripts/deploy-prod.sh`
  - Health check: `scripts/health_check.sh https://mini-crm.your-domain/healthz`

### Operations (quick)

- Health endpoint: `GET /healthz` returns `{status:"ok"}` and timestamp.
- DB backups: `scripts/backup_db.sh` (reads `.env`, supports SQLite/MySQL/PostgreSQL). Vars: `BACKUP_DIR`, `RETENTION`.
- Log rotation: `scripts/rotate_logs.sh` (vars: `LOG_DIR`, `BASE`, `RETENTION`).
- Suggested crons (example):
  - `0 2 * * * cd /var/www/mini-crm && BACKUP_DIR=/var/backups/mini-crm scripts/backup_db.sh`
  - `0 0 * * * cd /var/www/mini-crm && scripts/rotate_logs.sh`

### Post-deploy verification (quick checklist)

```bash
scripts/health_check.sh https://mini-crm.your-domain/healthz
php artisan migrate:status | tail -n +1
php artisan queue:failed
php artisan schedule:list | cat
```

### Incident runbooks

See `RUNBOOK.md` for quick guides for WhatsApp, telephony, and HubSpot outages.

### Go-live checklist

- Infra: domain and TLS active, backups scheduled, log rotation configured, queue workers under `supervisor` or `systemd`.
- App: `APP_KEY` generated, `.env` reviewed, migrations applied, admin user created.
- Observability: logs accessible, health checks monitored, basic alerts in place.
- Rollback plan: latest backup validated and documented procedure.
- Queues (worker): `php artisan queue:work`
- Clear caches: `php artisan optimize:clear`
- Tinker (REPL): `php artisan tinker`

### GDPR

Basic capabilities included:

- Consents: `Consent` model with channel, source, and timestamps. Service `App\Support\Gdpr\GdprService::recordConsent()`.
- Export: UI action and console command:
  - `php artisan gdpr:export {customer_id}` → creates a ZIP with `customer.json` under `storage/app/exports/gdpr/`.
- Right to be forgotten (anonymization): UI action and console:
  - `php artisan gdpr:erase {customer_id} --force`
  - Keeps historical appointments/activities while removing PII and unlinking customer FKs.
- Auditing: `audit_logs` records critical actions (export/erase/consent) including user and IP.

Security quick tips:

- Password policy: uses Laravel defaults; strengthen with custom validation as needed.
- CSRF: enabled by default in Blade forms (`@csrf`).
- Sessions: review `config/session.php` (SameSite, secure, expiration).
- Optional 2FA: if you install Breeze 2FA/Jetstream, it integrates with current auth.
