# ⭐️ ReviewTracker (Laravel SaaS Module)

An enterprise-grade **QR-code customer feedback & Google Review accelerator module** for Laravel applications.

ReviewTracker helps businesses convert in-person customer visits into glowing, authentic 5-star Google Reviews while privately catching negative feedback before it hits public review sites.

---

## 🚀 Key Features

### 1. 🤖 Zero-Input Auto-Generated Human Reviews
- **Natural & Authentic**: Automatically constructs realistic 2–3 sentence customer reviews using conversational language (*"Popped in today..."*, *"Super helpful staff..."*, *"Got everything sorted in under 5 minutes..."*).
- **No Form Fatigue**: Requires **zero typing or input from the customer** — no forms asking what they bought or when it was delivered.
- **Strict Session Deduplication**: Guaranteed to **never generate the same review twice** in a single session using a 5-layer combinatorial sentence engine.

### 2. 🎯 Local SEO Keywords Engine
- Automatically embeds high-value local search phrases into generated reviews:
  - `"best local shop"`
  - `"great customer service"`
  - `"clean store"`
  - `"fair prices"`
  - `"fast checkout"`
  - `"great selection"`
  - `"top quality products"`

### 3. 📱 Mobile-First UX & Pop-Up Blocker Immunity
- **Seamless Redirects**: Bypasses mobile Safari (iOS) and Chrome (Android) pop-up blockers with synchronous clipboard copying and direct window navigation.
- **Inline Copy Button**: Provides a dedicated **📋 Copy** button directly inside the review text area.
- **5-Star Visual Badging**: Displays a gold 5-star rating header to reinforce 5-star Google submissions.

### 4. 🛡️ 1-Tap Private Feedback (Reputation Defense)
- When a customer rates their experience as "Okay" or "Needs Improvement", they are redirected to a private internal feedback form.
- **1-Tap Quick Issue Chips**: Customers can tap issue chips in 1 second (⏱️ *Wait Time*, 💬 *Communication*, 💵 *Pricing*, 📦 *Stock*, 🧹 *Cleanliness*) without typing long paragraphs.
- Feedback is logged privately in your database for management review, protecting your public Google rating.

### 5. 📊 Admin & Employee Dashboards
- **Admin Portal**: Multi-company management, feedback inbox with status tracking, CSV exports, and performance analytics.
- **Employee Portal**: Dedicated employee dashboard with scan counters, personal QR codes, and fullscreen QR presentation mode.

---

## 📐 System Architecture & Flow

```
[ Customer Scans Employee QR Code ]
                 │
                 ▼
     GET /review/{employee}
  (Landing Page: 🤩 / 😐 / 😕)
                 │
      ┌──────────┴──────────┐
      │                     │
  [ 🤩 Great ]       [ 😐 / 😕 Okay or Bad ]
      │                     │
      ▼                     ▼
 GET /good/{employee}  GET /ok/{employee} or /bad/{employee}
 (Autogenerate Human   (Private Internal Feedback Form
  SEO Review + Copy)    + 1-Tap Quick Issue Chips)
      │                     │
      ▼                     ▼
 [ Open Google ]       [ Submit to DB ]
                            │
                            ▼
                   GET /thankyou (Confirmation)
```

---

## 🛠️ Installation & Setup Guide

### 1. Copy Module Files to Your Laravel App
Copy the module directories into your Laravel project root:

```bash
app/Http/Controllers/ReviewController.php
app/Http/Controllers/Admin/*
app/Http/Controllers/Employee/*
app/Models/Company.php
app/Models/Employee.php
app/Models/Feedback.php
database/migrations/*
routes/reviewtracker.php
resources/views/reviews/*.blade.php
```

### 2. Install QR Code Package
Install the simple-qrcode package via Composer:

```bash
composer require simplesoftwareio/simple-qrcode
```

### 3. Register Routes & Middleware
In `routes/web.php`:

```php
require __DIR__.'/reviewtracker.php';
```

In `bootstrap/app.php` (Laravel 11+):

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'employee.auth' => \App\Http\Middleware\EmployeeAuthenticated::class,
    ]);
})
```

### 4. Run Migrations & Storage Link

```bash
php artisan migrate
php artisan storage:link
```

---

## 📁 Directory Map

| Directory / File | Description |
|------------------|-------------|
| `app/Http/Controllers/ReviewController.php` | Public review funnel, rating capture & review generation |
| `resources/views/reviews/feedback.blade.php` | Customer landing page after scanning QR |
| `resources/views/reviews/good.blade.php` | Auto-generated human SEO review generator & copy page |
| `resources/views/reviews/internal_feedback.blade.php` | Private internal feedback form with 1-tap issue chips |
| `resources/views/reviews/thankyou.blade.php` | Warm confirmation screen |
| `database/migrations/` | Database schemas (`companies`, `employees`, `feedback`) |
| `docs/ARCHITECTURE.md` | In-depth technical architecture documentation |
| `docs/FEATURES_GUIDE.md` | Feature breakdown & business usage guide |

---

## 📄 License
This module is open-source software under the [MIT License](LICENSE).
