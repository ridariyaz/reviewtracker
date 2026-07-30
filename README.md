# ReviewTracker (Laravel)

Employee QR review funnel for businesses. Customers scan an employee QR, rate the experience, and “Good” ratings are redirected to Google Reviews while OK/Bad stay private.

> **Start here for code understanding:** [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md)

## Requirements

- PHP 8.2+
- Composer
- PostgreSQL

## Local setup

```bash
composer install
cp .env.example .env
php artisan key:generate
# Set DB_* for PostgreSQL in .env
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

Seeded admin:

- Username: `admin`
- Password: `password`

**Important:** In Company settings, set the company’s **Google review URL**. Otherwise “Good” falls back to `https://google.com`.

## Project map

| Path | Purpose |
|------|---------|
| `routes/web.php` | All HTTP routes |
| `app/Http/Controllers/` | Request handlers |
| `app/Models/` | Database models |
| `app/Services/` | QR codes, logos, active company |
| `resources/views/` | Blade UI |
| `database/migrations/` | Schema |
| `docs/ARCHITECTURE.md` | Product flow + code guide |
| `legacy-flask/` | Original Flask app (reference only) |

## Production settings

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
LOG_LEVEL=error
SESSION_SECURE_COOKIE=true
FILESYSTEM_DISK=public
DB_CONNECTION=pgsql
# ... your Postgres credentials
```

```bash
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan storage:link
```

## Docker

```bash
docker build -t reviewtracker .
docker run --rm -p 8080:8080 \
  -e APP_KEY=base64:... \
  -e APP_ENV=production \
  -e APP_DEBUG=false \
  -e APP_URL=http://localhost:8080 \
  -e DB_CONNECTION=pgsql \
  -e DB_HOST=host.docker.internal \
  -e DB_PORT=5432 \
  -e DB_DATABASE=reviewtracker \
  -e DB_USERNAME=apple \
  -e DB_PASSWORD= \
  reviewtracker
```

The container entrypoint runs migrations and starts Apache on `$PORT` (default `8080`).

## Heroku / Procfile hosts

```bash
git push heroku main
```

Uses `Procfile` (`heroku-php-apache2` + migrate release phase).

## Features

- Admin signup/login
- Multi-company branding + Google review URL
- Employee QR generation
- Customer review funnel (good → Google, ok/bad → private feedback)
- Feedback inbox + CSV export
- Analytics with Chart.js
- Employee portal (stats + fullscreen QR)
