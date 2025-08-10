## Mini-CRM

Pequeño CRM en Laravel 11 con Breeze (Blade), Pest y Bootstrap + Vite.

### Requisitos

- PHP 8.2+
- Composer 2.x
- Node.js 20+ y npm 10+
- SQLite3

### Cómo arrancar (repo ya clonado)

1) Instala dependencias, configura `.env` con SQLite, migra y seed:

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

2) Levanta los servidores de desarrollo (en dos terminales):

```bash
npm run dev
php artisan serve
```

Accede en `http://127.0.0.1:8000`.

### Credenciales demo (seed)

- Email: `admin@example.com`
- Password: `password`

### Crear un proyecto desde cero (alternativa)

Comandos listos para copiar/pegar que crean un proyecto Laravel 11, instalan Breeze (Blade), Pest, Bootstrap @latest con Vite y configuran SQLite. Ejecuta todo seguido en macOS:

```bash
# 1) Crear proyecto Laravel 11
composer create-project laravel/laravel:^11.0 mini-crm && cd mini-crm

# 2) Breeze (Blade)
composer require laravel/breeze --dev
php artisan breeze:install blade --no-interaction

# 3) Pest (tests)
composer require pestphp/pest pestphp/pest-plugin-laravel --dev
php artisan pest:install --no-interaction

# 4) Bootstrap @latest + Popper con Vite
npm install bootstrap @popperjs/core
# añade imports si no existen ya
grep -q "bootstrap/dist/css/bootstrap.min.css" resources/js/app.js || \
printf "\nimport 'bootstrap/dist/css/bootstrap.min.css';\nimport 'bootstrap';\n" >> resources/js/app.js

# 5) Configurar SQLite en .env
cp .env.example .env
[ -f database/database.sqlite ] || touch database/database.sqlite
sed -i '' \
  -e 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' \
  -e '/^DB_HOST=/ s/^/# /' \
  -e '/^DB_PORT=/ s/^/# /' \
  -e '/^DB_DATABASE=/ s/^/# /' \
  -e '/^DB_USERNAME=/ s/^/# /' \
  -e '/^DB_PASSWORD=/ s/^/# /' .env
php artisan key:generate

# 6) Migraciones + seed (crea usuario demo)
php artisan migrate:fresh --seed

# 7) Instalar dependencias frontend y levantar Vite
npm install
npm run dev

# 8) Arrancar Laravel
php artisan serve
```

### Horizon (opcional)

```bash
# Requiere Redis (extensión phpredis o paquete predis). Ejemplo con predis:
composer require laravel/horizon predis/predis
php artisan horizon:install
php artisan vendor:publish --tag=horizon-config
# Ajusta .env: QUEUE_CONNECTION=redis y REDIS_CLIENT=predis
php artisan migrate
php artisan horizon
```

### Comandos frecuentes

- **Vite (dev)**: `npm run dev`
- **Vite (build)**: `npm run build`
- **Servidor Laravel**: `php artisan serve`
- **Tests (Pest)**: `php artisan test` o `./vendor/bin/pest`
- **Re-seed rápido**: `php artisan migrate:fresh --seed`
### Informes y KPIs

- UI: menú `Informes` → tarjetas KPI + tabla.
- Filtro por `Fuente` (si existen `source` en leads): `Todas`, `Google`, `Meta`, `Organic`.
- API JSON autenticada: `GET /api/reports/kpis?source=google`.

Semillas demo crean leads con `source` `google`, `meta` y `organic`, citas asistidas/no-show y tratamientos con sesiones 6/6 para ver un panel “vivo”.

### CI y despliegue

- **CI (GitHub Actions)**: `.github/workflows/ci.yml` instala Composer, ejecuta `vendor/bin/pest` y `npm run build` en cada PR y push a `main`.
- **Deploy**:
  - Genérico: `bash scripts/deploy.sh /var/www/mini-crm php`
  - Staging: `HEALTHCHECK_URL="https://staging.tu-dominio/healthz" bash scripts/deploy-staging.sh`
  - Producción: `HEALTHCHECK_URL="https://mini-crm.tu-dominio/healthz" bash scripts/deploy-prod.sh`
  - Health check: `scripts/health_check.sh https://mini-crm.tu-dominio/healthz`

### Operaciones (breve)

- **Health endpoint**: `GET /healthz` devuelve `{status:"ok"}` y fecha.
- **Backups DB**: `scripts/backup_db.sh` (lee `.env`, soporta SQLite/MySQL/PostgreSQL). Variables: `BACKUP_DIR`, `RETENTION`.
- **Rotación de logs**: `scripts/rotate_logs.sh` (variables: `LOG_DIR`, `BASE`, `RETENTION`).
- **Crons sugeridos** (ejemplo):
  - `0 2 * * * cd /var/www/mini-crm && BACKUP_DIR=/var/backups/mini-crm scripts/backup_db.sh`
  - `0 0 * * * cd /var/www/mini-crm && scripts/rotate_logs.sh`

### Verificación post-deploy (checklist rápida)

```bash
scripts/health_check.sh https://mini-crm.tu-dominio/healthz
php artisan migrate:status | tail -n +1
php artisan queue:failed
php artisan schedule:list | cat
```

### Runbooks de incidentes

Consulta `RUNBOOK.md` para guías rápidas ante caída de WhatsApp, telefonía y HubSpot.

### Go-live checklist

- **Infra**: dominio y TLS activos, backups programados, rotación de logs configurada, workers de colas en `supervisor` o `systemd`.
- **App**: `APP_KEY` generado, variables `.env` revisadas, migraciones aplicadas, usuario admin creado.
- **Observabilidad**: logs accesibles, health checks monitorizados, alertas básicas configuradas.
- **Plan de rollback**: último backup validado, procedimiento documentado.

### Plan (hasta 20/09/2025)

- 01–09/08: Endurecimiento seguridad y pulido CI/CD.
- 10–17/08: Pruebas integrales y documentación operativa final.
- 18–24/08: Vacaciones (bloqueado).
- 25/08–05/09: Ensayos de despliegue y performance tuning.
- 08–12/09: UAT y correcciones.
- 15–19/09: Ventana de go-live, monitoreo intensivo.
- 20/09: Post-mortem y transición BAU.

- **Colas (worker)**: `php artisan queue:work`
- **Limpiar cachés**: `php artisan optimize:clear`
- **Tinker (REPL)**: `php artisan tinker`

### Notas

- Breeze instala vistas Blade con Tailwind por defecto. Bootstrap ya está disponible vía Vite; las vistas se adaptarán en las siguientes iteraciones (layout base, navegación y placeholders).

### GDPR

Cumplimiento básico incorporado:

- Consentimientos: modelo `Consent` con canal, fuente y timestamps. Servicio `App\Support\Gdpr\GdprService::recordConsent()`.
- Exportación: UI en `Clientes` (botón Exportar) y por consola:
  - `php artisan gdpr:export {customer_id}` → genera ZIP con `customer.json` en `storage/app/exports/gdpr/`.
- Derecho al olvido (anonimización): UI (botón Anonimizar) y consola:
  - `php artisan gdpr:erase {customer_id} --force`
  - Mantiene citas/actividades históricas, pero elimina PII y desvincula FK del cliente.
- Auditoría: `audit_logs` registra acciones críticas (export/erase/consent) con usuario e IP.

Seguridad rápida:

- Password policy: usa reglas por defecto de Laravel; refuérzalas con validación personalizada si procede.
- CSRF: habilitado automáticamente en formularios Blade (`@csrf`).
- Sesiones: revisa `config/session.php` (`SameSite`, `secure`, expiración).
- 2FA opcional: si instalas Breeze con 2FA/Jetstream, se integra con `auth` actual.
