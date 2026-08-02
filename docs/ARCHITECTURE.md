# ReviewTracker Technical Architecture

This document provides a comprehensive overview of the **ReviewTracker** module architecture, data flows, database design, and key components.

---

## 1. Executive Summary

ReviewTracker is a high-converting **QR Code → Review Acceleration & Reputation Gateway** designed for businesses. It solves two critical challenges:
1. **Public Review Growth**: Increases 5-star Google Reviews by autogenerating human, local SEO-optimized reviews that customers can copy and paste with a single tap.
2. **Reputation Protection**: Diverts neutral ("Okay") or negative ("Bad") feedback into a private internal feedback system with 1-tap issue chips, preventing public negative reviews.

---

## 2. Component Diagram

```
┌────────────────────────────────────────────────────────────────────────┐
│                          Customer Smartphone                           │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ Scan QR Code
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│                        Public Review Funnel                            │
│                                                                        │
│   GET /review/{employee}  ──►  Landing View (feedback.blade.php)      │
│                                           │                            │
│                     ┌─────────────────────┴─────────────────────┐      │
│                     │                                           │      │
│          [ 🤩 Great Rating ]                           [ 😐 / 😕 Rating ]│
│                     │                                           │      │
│                     ▼                                           ▼      │
│            GET /good/{employee}                        GET /ok/{employee} │
│          (good.blade.php view)                    (internal_feedback.blade)│
│                     │                                           │      │
│  - Auto-Generate 5-Layer SEO Review                             │      │
│  - Session Deduplication Set                             - 1-Tap Chips │
│  - 1-Tap Copy & Open Google                              - Submit to DB│
│                     │                                           │      │
│                     ▼                                           ▼      │
│             Google Review URL                         GET /thankyou    │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 3. Database Schema

### `companies` Table
Stores company configuration, branding, and target review URLs.

| Column | Type | Description |
|--------|------|-------------|
| `id` | `bigint` (Primary Key) | Unique company ID |
| `user_id` | `bigint` (Foreign Key) | Owner admin user ID |
| `name` | `string` | Company brand name |
| `logo_url` | `string` (Nullable) | Brand logo URL |
| `primary_color` | `string` | Hex primary theme color (e.g. `#0d6efd`) |
| `secondary_color` | `string` | Hex secondary theme color (e.g. `#020617`) |
| `google_review_url` | `string` (Nullable) | Target Google Maps / Review URL |

### `employees` Table
Stores staff members associated with a company.

| Column | Type | Description |
|--------|------|-------------|
| `id` | `bigint` (Primary Key) | Unique employee ID |
| `company_id` | `bigint` (Foreign Key) | Associated company ID |
| `name` | `string` | Staff member full name |
| `scans` | `integer` | Total QR code scan count |
| `good_count` | `integer` | Count of positive reviews |
| `ok_count` | `integer` | Count of neutral reviews |
| `bad_count` | `integer` | Count of negative reviews |
| `employee_username`| `string` (Nullable) | Login username for employee portal |
| `employee_password`| `string` (Nullable) | Hashed login password |

### `feedback` Table
Stores all logged customer ratings and private internal comments.

| Column | Type | Description |
|--------|------|-------------|
| `id` | `bigint` (Primary Key) | Unique feedback log ID |
| `company_id` | `bigint` (Foreign Key) | Target company ID |
| `employee_id` | `bigint` (Foreign Key) | Target employee ID |
| `rating` | `enum('good','ok','bad')` | Rating selection |
| `comment` | `text` | Private customer feedback text |
| `status` | `string` | Feedback status (`new`, `reviewed`, `resolved`) |

---

## 4. Key Engines & Algorithms

### 4.1 Combinatorial Local SEO Review Generator
The review generator in `resources/views/reviews/good.blade.php` operates as a 5-layer combinatorial sentence matrix:

```
Review = [Opener] + [Practical Detail & SEO Keyword] + [Closing Recommendation]
```

- **Openers**: 8 natural conversational starters (*"Popped into {company} today..."*).
- **Details**: 8 practical customer concerns containing Local SEO terms (*"clean store"*, *"fair prices"*, *"fast checkout"*).
- **Closings**: 6 local business recommendation finishers (*"Definitely my new go-to local shop"*).

#### Deduplication Mechanism:
A JavaScript `seenReviews` `Set` tracks generated review hashes during the user's session, guaranteeing that `generateNewReview()` **never produces identical review text twice**.

### 4.2 Mobile Pop-up Prevention
To bypass mobile browser (iOS Safari / Android Chrome) pop-up blockers:
1. Clipboard write (`navigator.clipboard.writeText`) occurs synchronously inside the touch/click event handler.
2. Window navigation executes immediately via `window.open(url, '_blank')` or direct location fallback (`window.location.href`).

---

## 5. Security & Validation

1. **CSRF Protection**: All POST routes enforce `@csrf` token validation.
2. **Mass Assignment Protection**: Models declare `$fillable` arrays to prevent unauthorized column updates.
3. **Password Security**: Employee passwords are hidden in model serialization (`$hidden = ['employee_password']`).
