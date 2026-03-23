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
| 🟠 Database | MySQL + PDO | Data storage & queries |
| 🎨 Frontend | HTML + Tailwind CSS | UI & styling |
| 🖥️ Server | XAMPP (Apache) | Local development |
| 🔐 Auth | `$_SESSION` + `password_hash()` | Login & security |
| 🧩 Pattern | Singleton `DatabaseManager` | DB connection |

---

## 📁 Project Structure

```
📦 Subscription-Box/
│
├── 📂 app/
│   ├── 📂 Controllers/       ← Handle HTTP requests
│   ├── 📂 Models/            ← Database logic (PDO)
│   └── 📂 Views/             ← HTML templates
│
├── 📂 config/
│   ├── database.php          ← DB credentials
│   └── app.php               ← App settings
│
├── 📂 core/
│   ├── DatabaseManager.php   ← Singleton Pattern ⭐
│   ├── Router.php            ← Manual routing
│   └── Session.php           ← $_SESSION wrapper
│
├── 📂 database/
│   └── 📂 migrations/        ← SQL schema files
│
├── 📂 docs/
│   ├── 📂 diagrams/          ← Visual Paradigm UML files
│   └── README.md
│
├── 📂 public/
│   └── index.php             ← Entry point
│
├── 📂 routes/
│   └── web.php               ← Route definitions
│
└── 📄 README.md
```

---

## 🏗️ Architecture

```
┌─────────────────────────────────────────────┐
│                  Browser                    │
└─────────────────┬───────────────────────────┘
                  │ HTTP Request
┌─────────────────▼───────────────────────────┐
│            public/index.php                 │
│               (Entry Point)                 │
└─────────────────┬───────────────────────────┘
                  │
┌─────────────────▼───────────────────────────┐
│              Router (Manual)                │
└──────┬──────────────────────────────────────┘
       │
┌──────▼──────────┐    ┌─────────────────────┐
│   Controllers   │───▶│      Models         │
│  (Business Logic│    │  (PDO + Raw SQL)    │
└──────┬──────────┘    └──────────┬──────────┘
       │                          │
┌──────▼──────────┐    ┌──────────▼──────────┐
│     Views       │    │  DatabaseManager    │
│  (HTML/Tailwind)│    │  (Singleton ⭐)     │
└─────────────────┘    └─────────────────────┘
```

### ⭐ Singleton Pattern

```php
// core/DatabaseManager.php
class DatabaseManager {
    private static ?DatabaseManager $instance = null;
    private PDO $connection;

    private function __construct() {
        $this->connection = new PDO(
            "mysql:host=localhost;dbname=subscription_box",
            "root", ""
        );
    }

    public static function getInstance(): DatabaseManager {
        if (self::$instance === null) {
            self::$instance = new DatabaseManager();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->connection;
    }
}
```

### 🔐 Auth — PHP Sessions (No Framework)

```php
// Login
session_start();
$_SESSION['user_id']   = $user['id'];
$_SESSION['user_role'] = $user['role'];

// Role Check
if ($_SESSION['user_role'] !== 'admin') {
    header('Location: /403'); exit;
}

// Password
password_hash($password, PASSWORD_BCRYPT);
password_verify($input, $stored_hash);
```

---

## 🚀 How To Run

**Step 1** — Clone or copy the project into XAMPP `htdocs`
```
C:/xampp/htdocs/Subscription-Box/
```

**Step 2** — Start Apache & MySQL from XAMPP Control Panel

**Step 3** — Import the database
```
phpMyAdmin → New Database → subscription_box
→ Import → database/migrations/001_create_users_table.sql
```

**Step 4** — Configure your settings
```php
// config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'subscription_box');
define('DB_USER', 'root');
define('DB_PASS', '');
```

**Step 5** — Open in browser
```
http://localhost/Subscription-Box/public
```

---

## 📦 Modules

| # | Module | Key Features |
|---|---|---|
| 1 | 🔐 Auth | Register · Login · Logout · Role Check |
| 2 | 📋 Subscription Management | Tiers · Pause/Resume · Upgrade/Downgrade |
| 3 | 🔄 Customization Engine | Swap Items · Allergy Filter · Lock-in Protocol |
| 4 | 🚚 Logistics Tracker | Status Machine · Last-Mile · Delivery Instructions |
| 5 | 🎁 Referral & Rewards | Attribution · Tiered Rewards · Promo Codes |
| 6 | 🛠️ Admin Panel | RBAC · Audit Trail · Inventory Alerts |

---

## 👥 Team Workflow

| Member | Responsibility |
|---|---|
| 👤 Member 1 | Auth · Sessions · RBAC · Singleton DB |
| 👤 Member 2 | Subscription Plans · Tier Logic · Pause/Resume |
| 👤 Member 3 | Swap Engine · Allergy Filter · Customization |
| 👤 Member 4 | Logistics · Delivery Status · Notifications |
| 👤 Member 5 | Referral System · Promo Codes · Admin Panel |
| 👤 Member 6 | Frontend (Tailwind) · Views · AJAX (1 scenario) |

> **Rule:** Every member writes their own Controllers + Models using raw SQL via PDO. No Eloquent. No shortcuts.

---

## 📄 Documentation

All UML diagrams are stored inside `docs/diagrams/` and created using **Visual Paradigm Community**.

| Diagram | Status |
|---|---|
| ✅ Use Case Diagram | Phase 1 |
| ✅ Activity / Swimlane Diagrams | Phase 1 |
| ✅ Class Diagram | Phase 1 |
| ✅ Sequence Diagrams | Phase 1 |
| ✅ ERD | Phase 1 |
| ✅ Object Diagrams | Phase 1 |
| ✅ Communication Diagrams | Phase 1 |
| ✅ Package Diagrams | Phase 1 |

---

## ⚠️ Important Notes

```
✅ DO    Use PDO for all database connections
✅ DO    Write raw SQL for every CRUD operation
✅ DO    Implement Singleton pattern manually
✅ DO    Use $_SESSION for auth — not any library
✅ DO    Use password_hash() / password_verify()

❌ DON'T Use any ORM or query builder
❌ DON'T Use ready-made frontend templates
❌ DON'T Use framework authentication helpers
```

> 🔑 **Admin Account:** After registering, manually set `role = 'admin'` in phpMyAdmin to access the admin panel.

---

<div align="center">

**CS251 Software Engineering 1 — Spring 2026**
Capital University · Faculty of Computing & Artificial Intelligence

</div>
