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

- Email: `test@example.com`
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
- **Colas (worker)**: `php artisan queue:work`
- **Limpiar cachés**: `php artisan optimize:clear`
- **Tinker (REPL)**: `php artisan tinker`

### Notas

- Breeze instala vistas Blade con Tailwind por defecto. Bootstrap ya está disponible vía Vite; las vistas se adaptarán en las siguientes iteraciones (layout base, navegación y placeholders).
