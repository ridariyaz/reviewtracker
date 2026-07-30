# How ReviewTracker works

This document explains the product flow and where the code lives so new contributors can orient quickly.

## What the product does

ReviewTracker is a **QR → review funnel** for businesses:

1. An **admin** creates a company and employees.
2. Each employee gets a unique **QR code** pointing at `/review/{employee}`.
3. A customer scans the QR and picks **Good / OK / Bad**.
4. **Good** sends them to the company’s public **Google review URL** (and records a good rating).
5. **OK / Bad** ask for private internal comments (not posted to Google).
6. Admins see feedback, analytics, and CSV exports. Employees can log in to see their own QR and stats.

If `companies.google_review_url` is empty, “Good” falls back to `https://google.com`.

## High-level architecture

```
Browser
  │
  ▼
routes/web.php          ← all HTTP routes
  │
  ▼
Http/Controllers/*      ← request handling
  │
  ├── Models/*          ← Eloquent + DB tables
  ├── Services/*        ← QR, logo colors, current company
  └── resources/views/* ← Blade UI
```

- **Auth (admins):** Laravel `web` guard → `users` table (`AuthController`).
- **Auth (employees):** separate `employee` guard → `employees` table (`EmployeePortalController`).
- **Current company:** stored in session as `company_id` (`CompanyContext`).

## Database (main tables)

| Table | Purpose |
|-------|---------|
| `users` | Admin accounts (`username` + `password`) |
| `companies` | Branding, logo, colors, Google review URL; owned by a user |
| `employees` | Staff under a company; scan counters; optional employee login |
| `feedback` | Ratings (`good` / `ok` / `bad`), comments, status |

Migrations live in `database/migrations/`.

## Customer review funnel (public, no login)

| Step | Route | Controller method |
|------|-------|-------------------|
| Landing | `GET /review/{employee}` | `ReviewController@show` |
| Good | `GET /good/{employee}` | `ReviewController@good` → redirect to Google review URL |
| OK | `GET /ok/{employee}` | `ReviewController@ok` → private comment form |
| Bad | `GET /bad/{employee}` | `ReviewController@bad` → private comment form |
| Submit private | `POST /submit_internal_feedback` | `ReviewController@submitInternal` |
| Thanks | `GET /thankyou` | `ReviewController@thankyou` |

QR PNGs are generated into `storage/app/public/qrcodes/{id}.png` and served via `/storage/...` (`php artisan storage:link`).

## Admin area (middleware `admin`)

| Area | Routes | Controller |
|------|--------|------------|
| Dashboard | `/admin` | `AdminController` |
| Companies | `/companies/*` | `CompanyController` |
| Employees | `/employees`, `/add_employee`, … | `EmployeeController` |
| Feedback inbox | `/feedback` | `FeedbackController` |
| Analytics | `/analytics` | `AnalyticsController` |
| CSV | `/export/*.csv` | `FeedbackController` |

## Employee portal (middleware `employee`)

| Area | Routes | Controller |
|------|--------|------------|
| Login | `/employee/login` | `EmployeePortalController` |
| Dashboard | `/employee/dashboard` | stats, feedback, leaderboard, QR |
| Fullscreen QR | `/employee/qr` | large QR view |

Employee logins are created by an admin via “Set login” on the dashboard (`employee_username` + `employee_password` on `employees`).

## Important services

| Class | Role |
|-------|------|
| `App\Services\CompanyContext` | Resolve / switch the admin’s active company |
| `App\Services\QrCodeService` | Build PNG QR for an employee review URL |
| `App\Services\LogoService` | Store uploaded logo + extract brand colors |

## Views

Blade templates under `resources/views/`:

- `layouts/` — shared admin / auth chrome
- `auth/` — admin login & signup
- `admin/`, `companies/`, `employees/`, `feedback/`, `analytics/` — admin UI
- `review/` — public customer screens
- `employee/` — employee portal

## Auth middleware

Registered in `bootstrap/app.php`:

- `admin` → `EnsureAdmin` (must be logged in as a user)
- `employee` → `EnsureEmployee` (must be logged in as an employee)

## Config & deploy notes

- App config: `.env` (see `.env.example`)
- Production: set `APP_DEBUG=false`, real `APP_URL`, Postgres `DB_*`, and a real Google review URL per company
- Docker: `Dockerfile` + `docker/entrypoint.sh` (runs migrations, starts Apache)
- Legacy Flask reference only: `legacy-flask/`

## Typical change examples

- **Change “Good” redirect behavior** → `ReviewController::good`
- **Change QR generation** → `QrCodeService` + `EmployeeController::store`
- **Add a new admin page** → route in `routes/web.php` (inside `admin` group) + controller + Blade view
- **Change schema** → new migration under `database/migrations/`
