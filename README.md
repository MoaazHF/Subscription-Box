<div align="center">

<img src="https://img.shields.io/badge/CS251-Software%20Engineering%201-blue?style=for-the-badge&logo=academia&logoColor=white" />
<img src="https://img.shields.io/badge/Spring-2026-green?style=for-the-badge" />
<img src="https://img.shields.io/badge/Capital%20University-FCAI-red?style=for-the-badge" />

<br/><br/>

# 📦 Subscription Box Portal

### *A Customization & Delivery Platform — CS251 Group Project*

<br/>

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat-square&logo=mysql&logoColor=white)
![Tailwind](https://img.shields.io/badge/Tailwind_CSS-3.x-06B6D4?style=flat-square&logo=tailwindcss&logoColor=white)
![XAMPP](https://img.shields.io/badge/XAMPP-Local_Server-FB7A24?style=flat-square&logo=xampp&logoColor=white)
![VS Code](https://img.shields.io/badge/VS_Code-IDE-007ACC?style=flat-square&logo=visualstudiocode&logoColor=white)

</div>

---

## 🗂️ Table of Contents

- [Overview](#-overview)
- [Tech Stack](#-tech-stack)
- [Project Structure](#-project-structure)
- [Architecture](#-architecture)
- [How To Run](#-how-to-run)
- [Modules](#-modules)
- [Team Workflow](#-team-workflow)
- [Documentation](#-documentation)
- [Important Notes](#-important-notes)

---

## 🌟 Overview

A **Subscription-as-a-Service (SaaS)** web platform where users subscribe to monthly themed boxes and customize their contents. Built for CS251 Software Engineering 1 — Spring 2026.

> Users can swap items, pause subscriptions, track deliveries, and earn referral rewards — all through a clean, role-based interface.

**Inspired by:** [HelloFresh](https://www.hellofresh.com/) · [LootCrate](https://www.lootcrate.com/)

---

## 🛠️ Tech Stack

| Layer | Technology | Purpose |
|---|---|---|
| 🔵 Backend | PHP 8.2 Native | Core logic, MVC, CRUD |
| 🟠 Database | MySQL + PDO | Data storage & raw SQL queries |
| 🎨 Frontend | HTML + Tailwind CSS | UI & styling |
| 🖥️ Server | XAMPP (Apache) | Local development |
| 🔐 Auth | `$_SESSION` + `password_hash()` | Login & security |
| 🧩 Pattern | Singleton `DatabaseManager` | DB connection |

---

## 📁 Project Structure

```
📦 subscription-box-portal/
│
├── 📂 public/                         ← Web root (Apache points here)
│   ├── index.php                      ← Front controller
│   ├── .htaccess                      ← URL rewriting
│   ├── 📂 css/
│   │   └── style.css
│   └── 📂 assets/                     ← Images, icons, fonts
│
├── 📂 app/
│   │
│   ├── 📂 Config/
│   │   └── config.php                 ← DB credentials & app settings
│   │
│   ├── 📂 Core/                       ← Base classes (framework layer)
│   │   ├── DatabaseManager.php        ← ⭐ Singleton Pattern
│   │   ├── Controller.php             ← Base controller
│   │   └── Model.php                  ← Base model (PDO wrapper)
│   │
│   ├── 📂 Controllers/
│   │   ├── HomeController.php
│   │   ├── AuthController.php         ← Login / Register / Logout
│   │   ├── SubscriptionController.php
│   │   ├── UserController.php
│   │   └── AdminController.php
│   │
│   ├── 📂 Models/
│   │   ├── User.php
│   │   ├── Subscription.php
│   │   ├── Box.php
│   │   └── Order.php
│   │
│   ├── 📂 Views/
│   │   ├── 📂 layouts/
│   │   │   ├── header.php
│   │   │   ├── footer.php
│   │   │   ├── navbar.php
│   │   │   └── sidebar.php
│   │   ├── 📂 auth/
│   │   │   ├── login.php
│   │   │   └── register.php
│   │   ├── 📂 home/
│   │   │   └── index.php
│   │   ├── 📂 subscriptions/
│   │   │   ├── browse.php
│   │   │   ├── detail.php
│   │   │   └── checkout.php
│   │   ├── 📂 user/
│   │   │   ├── dashboard.php
│   │   │   └── orders.php
│   │   └── 📂 admin/
│   │       ├── dashboard.php
│   │       ├── users.php
│   │       ├── boxes.php
│   │       └── orders.php
│   │
│   └── 📂 Helpers/
│       ├── AuthHelper.php             ← Session & role checks
│       ├── ValidationHelper.php       ← Input validation
│       └── Utils.php                  ← Shared utilities
│
├── 📂 database/
│   ├── schema.sql                     ← Table definitions
│   └── seeders.sql                    ← Sample data
│
├── 📂 docs/
│   ├── ER_Diagram.vpp
│   ├── Use_Case.vpp
│   ├── Class_Diagram.vpp
│   ├── Sequence_Diagram.vpp
│   └── README.md
│
├── composer.json
├── tailwind.config.js
└── README.md
```

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────────┐
│                  Browser                    │
└─────────────────┬───────────────────────────┘
                  │ HTTP Request
┌─────────────────▼───────────────────────────┐
│         public/index.php                    │
│         (Front Controller)                  │
└─────────────────┬───────────────────────────┘
                  │ .htaccess URL Rewrite
┌─────────────────▼───────────────────────────┐
│           Manual Router                     │
└──────┬──────────────────────────────────────┘
       │
┌──────▼──────────────┐    ┌──────────────────┐
│    Controllers/     │───▶│    Models/       │
│  Business Logic     │    │  Raw SQL + PDO   │
└──────┬──────────────┘    └───────┬──────────┘
       │                           │
┌──────▼──────────────┐    ┌───────▼──────────┐
│      Views/         │    │  Core/           │
│   HTML + Tailwind   │    │  DatabaseManager │
└─────────────────────┘    │  (Singleton ⭐)  │
                           └──────────────────┘
```


```

---

## 🚀 How To Run

**Step 1** — Copy project into XAMPP htdocs
```
C:/xampp/htdocs/subscription-box-portal/
```

**Step 2** — Start Apache & MySQL from XAMPP Control Panel

**Step 3** — Create the database
```
phpMyAdmin → New Database → subscription_box
→ Import → database/schema.sql
→ Import → database/seeders.sql
```

**Step 4** — Configure credentials
```php
// app/Config/config.php
return [
    'host'   => 'localhost',
    'dbname' => 'subscription_box',
    'user'   => 'root',
    'pass'   => '',
];
```

**Step 5** — Open in browser
```
http://localhost/subscription-box-portal/public
```

---

## 📦 Modules

| # | Module | Controller | Key Features |
|---|---|---|---|
| 1 | 🔐 Auth | `AuthController` | Register · Login · Logout · Role Guard |
| 2 | 📋 Subscriptions | `SubscriptionController` | Browse · Checkout · Pause · Upgrade |
| 3 | 👤 User Area | `UserController` | Dashboard · Orders · Swap Items |
| 4 | 🛠️ Admin Panel | `AdminController` | Users · Boxes · Orders · Reports |
| 5 | 🏠 Home | `HomeController` | Landing Page · Plans · Referral |

---

## 👥 Team Workflow

| Member | Files to Own |
|---|---|
| 👤 Member 1 | `Core/` · `AuthController` · `Helpers/AuthHelper` |
| 👤 Member 2 | `Models/Subscription.php` · `SubscriptionController` · subscription views |
| 👤 Member 3 | `Models/Box.php` · Swap logic · Customization views |
| 👤 Member 4 | `Models/Order.php` · Logistics · Delivery status views |
| 👤 Member 5 | Referral logic · Promo codes · `AdminController` |
| 👤 Member 6 | All Views layouts · Tailwind styling · AJAX (1 scenario) |

> **Rule:** Every member writes their own Models using raw SQL via PDO.
> No ORM. No shortcuts. Every query must be visible and reviewable.

---

## 📄 Documentation

All diagrams stored in `docs/` — created with **Visual Paradigm Community**.

| Diagram | File | Phase |
|---|---|---|
| ✅ Use Case Diagram | `Use_Case.vpp` | Phase 1 |
| ✅ Class Diagram | `Class_Diagram.vpp` | Phase 1 |
| ✅ Sequence Diagrams | `Sequence_Diagram.vpp` | Phase 1 |
| ✅ ER Diagram | `ER_Diagram.vpp` | Phase 1 |
| ⬜ Activity / Swimlane | — | Phase 1 |
| ⬜ Object Diagrams | — | Phase 1 |
| ⬜ Communication Diagrams | — | Phase 1 |
| ⬜ Package Diagrams | — | Phase 1 |

---


> 🔑 **Admin Account:** After registering, manually set `role = 'admin'`
> in phpMyAdmin to access the admin panel.

---
<div align="center">

**CS251 Software Engineering 1 — Spring 2026**

Capital University · Faculty of Computing & Artificial Intelligence · Computer Science Department

</div>

