# Subscription Box Platform

Enterprise-grade subscription commerce platform built with Laravel.

![Project Cover](public/cover.png)
<!-- REPLACE: docs/readme-media/cover-dashboard-light.png -->

## Overview

Subscription Box Platform unifies customer subscriptions, billing records, monthly box operations, delivery workflows, and admin operations into one system.

Core capabilities:
- Account registration and role-based access (subscriber, admin, driver, warehouse staff)
- Subscription lifecycle with payment simulation and transaction records
- Box generation and customization workflows
- Delivery tracking, claims handling, and operations visibility
- Admin control panels for products, drivers, warehouse staff, and delivery zones
- Growth modules: referrals, rewards, promo codes, gift subscriptions, social posts

## Product Demo

### Full Product Walkthrough
![Full Walkthrough](public/WebSiteWalkthrough.gif)
<!-- REPLACE: docs/readme-media/demo-full-flow.gif -->

## Screenshots

## Tech Stack

- PHP 8.4
- Laravel 13
- Tailwind CSS v4 + Vite
- PostgreSQL
- PHPUnit 12
- Lucide Icons
- Leaflet/OpenStreetMap (mapping-related modules)

## Architecture Snapshot

- `app/Http/Controllers` — application and operations controllers
- `app/Services` — core domain operations and business logic
- `app/Http/Requests` — validation layer
- `resources/views` — Blade UI layer
- `routes/web.php` — route definitions and middleware grouping
- `tests/Feature` — feature-level behavior verification

## Getting Started

### 1) Requirements

- PHP 8.4+
- Composer 2+
- Node.js 20+
- npm
- PostgreSQL 16+
- Git

Optional:
- Docker + Docker Compose

### 2) Clone

```bash
git clone https://github.com/MoaazHF/Subscription-Box.git
cd Subscription-Box
```

### 3) Install Dependencies

```bash
composer install
npm install
```

### 4) Configure Environment

Create `.env` in project root.

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8005

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
CACHE_STORE=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=local

VITE_APP_NAME="${APP_NAME}"
```

Generate app key:

```bash
php artisan key:generate
```

### 5) Start Database

Docker:

```bash
docker compose up -d
```

Services:
- PostgreSQL: `127.0.0.1:5435`
- pgAdmin: `http://127.0.0.1:5050`

### 6) Migrate + Seed

```bash
php artisan migrate --seed
php artisan storage:link
```

### 7) Run App

Recommended:

```bash
composer run dev
```

## Default Seeded Accounts

- Subscriber: `test@example.com` / `password`
- Admin: `admin@example.com` / `password`
- Driver: `driver@example.com` / `password`
- Warehouse staff: `warehouse_staff@example.com` / `password`


### DB connection refused

- verify PostgreSQL is running
- verify `.env` DB host/port/user/password
- if using Docker:

```bash
docker compose ps
```

### Product images not visible

```bash
php artisan storage:link
```

