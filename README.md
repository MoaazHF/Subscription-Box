# Subscription Box Platform

Laravel 13 application for subscription commerce operations.

The platform covers:
- customer signup and subscription lifecycle
- payment records and simulated payment gateway
- monthly box provisioning and customization
- delivery tracking and claims
- admin operations (products, drivers, warehouse staff, zones)
- growth modules (referrals, rewards, promo codes, gifts, flash sales, social posts)

## 1) Requirements

Install these first:
- PHP 8.3+
- Composer 2+
- Node.js 20+ and npm
- PostgreSQL 16+ (or Docker)
- Git

Optional but recommended:
- Docker + Docker Compose (for PostgreSQL + pgAdmin)

## 2) Clone Repository

```bash
git clone https://github.com/MoaazHF/Subscription-Box.git
cd Subscription-Box
```

## 3) Install Dependencies

```bash
composer install
npm install
```

## 4) Environment Configuration

This repository does not include `.env.example`.
Create `.env` manually in project root.

Use this baseline:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8005

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5435
DB_DATABASE=subscription_box
DB_USERNAME=subscription_box
DB_PASSWORD=secret

DB_TEST_DATABASE=subscription_box_test
FORWARD_DB_PORT=5435
FORWARD_PGADMIN_PORT=5050
PGADMIN_DEFAULT_EMAIL=admin@subscription-box.project
PGADMIN_DEFAULT_PASSWORD=secret

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

VITE_APP_NAME="${APP_NAME}"
```

Generate app key:

```bash
php artisan key:generate
```

## 5) Start Database

### Option A: Docker (recommended)

```bash
docker compose up -d
```

Services:
- PostgreSQL: `127.0.0.1:5435`
- pgAdmin: `http://127.0.0.1:5050`

### Option B: Local PostgreSQL

Create databases and user matching `.env`:
- database: `subscription_box`
- test database: `subscription_box_test`
- user: `subscription_box`
- password: `secret`
- port: `5435` (or adjust `.env`)

## 6) Run Migrations and Seeders

```bash
php artisan migrate --seed
```

## 7) Link Public Storage (required for uploaded product images)

```bash
php artisan storage:link
```

## 8) Start Application

### Full local dev stack (recommended)

```bash
composer run dev
```

This starts:
- Laravel server on `http://127.0.0.1:8005`
- queue listener
- Laravel logs stream
- Vite dev server

### Manual start (alternative)

Terminal 1:
```bash
php artisan serve --host=127.0.0.1 --port=8005
```

Terminal 2:
```bash
php artisan queue:listen --tries=1 --timeout=0
```

Terminal 3:
```bash
npm run dev
```

## 9) Seeded Login Accounts

After `php artisan migrate --seed`:

- Subscriber:
  - email: `test@example.com`
  - password: `password`

- Admin:
  - email: `admin@example.com`
  - password: `password`

- Driver:
  - email: `driver@example.com`
  - password: `password`

- Warehouse staff:
  - email: `warehouse@example.com`
  - password: `password`

## 10) Main URLs

- Home: `http://127.0.0.1:8005/`
- Login: `http://127.0.0.1:8005/login`
- Register: `http://127.0.0.1:8005/register`
- Plans: `http://127.0.0.1:8005/plans`
- Documentation center: `http://127.0.0.1:8005/docs`

Admin operation boards:
- Products: `http://127.0.0.1:8005/ops/products`
- Drivers: `http://127.0.0.1:8005/ops/drivers`
- Warehouse staff: `http://127.0.0.1:8005/ops/warehouse-staff`
- Delivery zones: `http://127.0.0.1:8005/ops/delivery-zones`

## 11) Admin CRUD Workflows

### Products
Admin can:
- create product
- upload product image
- update product
- replace/remove image
- delete product

Images are stored on `public` disk and served through `storage` symlink.

### Warehouse Staff Accounts
Admin can:
- create warehouse staff account with email + initial password
- set warehouse location
- update profile location
- delete profile/account

First login behavior:
- newly created warehouse staff accounts are forced to change password
- user is redirected to `/account/password-change` until password is updated

## 12) Subscription Payment Simulation

On `/subscriptions`:
- pressing `Start subscription` opens simulated gateway modal
- approve/decline transaction is captured
- payment transaction is written to `payments` table with gateway reference
- failed payment suspends subscription and stores failed transaction

## 13) Testing

Run all tests:

```bash
php artisan test --compact
```

Run specific file:

```bash
php artisan test --compact tests/Feature/AdminOperationsFlowTest.php
```

## 14) Build for Production Assets

```bash
npm run build
```

## 15) Useful Maintenance Commands

Clear caches:

```bash
php artisan optimize:clear
```

Rebuild view cache:

```bash
php artisan view:cache
```

Recreate database from scratch:

```bash
php artisan migrate:fresh --seed
```

## 16) Troubleshooting

### A) `Vite manifest` not found
Run:
```bash
npm run build
```
or keep dev server running:
```bash
npm run dev
```

### B) Database connection refused
- ensure PostgreSQL is running
- ensure `.env` DB values match actual host/port/user/password
- if using Docker, confirm containers are up:
```bash
docker compose ps
```

### C) Product images not visible
Ensure storage link exists:
```bash
php artisan storage:link
```

### D) SQLite test error: `could not find driver`
Install PDO SQLite extension for PHP, or run tests using configured PostgreSQL test database.

---

Project stack:
- Laravel 13
- PHP 8.3+
- PostgreSQL
- Tailwind CSS v4 + Vite
- Leaflet/OpenStreetMap integrations
- Lucide icons
