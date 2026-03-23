<div align="center">
  <h1>📦 Subscription Box Portal</h1>
  <p><b>CS251 Software Engineering 1 — Group Project</b></p>
  <p><i>Simple course-friendly template built with native PHP MVC, PDO, MySQL, Tailwind CSS, and XAMPP.</i></p>

  <p>
    <img src="https://img.shields.io/badge/PHP-Native-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
    <img src="https://img.shields.io/badge/MySQL-PDO-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL" />
    <img src="https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind CSS" />
    <img src="https://img.shields.io/badge/XAMPP-Server-FB7A24?style=for-the-badge&logo=xampp&logoColor=white" alt="XAMPP" />
  </p>
</div>

---

## 🏗️ Technical Stack

* **Backend:** Native PHP with manual MVC
* **Database:** MySQL + PDO
* **Frontend:** HTML + Tailwind CSS
* **Authentication:** `$_SESSION` + `password_hash()` + `password_verify()`
* **Authorization:** Manual role checks inside controllers
* **Design Pattern:** Singleton `DatabaseManager`

---

## 📂 Project Structure

```text
subscription-box-portal/
│
├── public/
│ ├── index.php
│ ├── .htaccess
│ ├── css/
│ │ └── style.css
│ └── assets/
│
├── app/
│ ├── Config/
│ │ └── config.php
│ ├── Core/
│ │ ├── DatabaseManager.php
│ │ ├── Controller.php
│ │ └── Model.php
│ ├── Controllers/
│ │ ├── HomeController.php
│ │ ├── AuthController.php
│ │ ├── SubscriptionController.php
│ │ ├── UserController.php
│ │ └── AdminController.php
│ ├── Models/
│ │ ├── User.php
│ │ ├── Subscription.php
│ │ ├── Box.php
│ │ └── Order.php
│ ├── Views/
│ │ ├── layouts/
│ │ │ ├── header.php
│ │ │ ├── footer.php
│ │ │ ├── navbar.php
│ │ │ └── sidebar.php
│ │ ├── auth/
│ │ │ ├── login.php
│ │ │ └── register.php
│ │ ├── home/
│ │ │ └── index.php
│ │ ├── subscriptions/
│ │ │ ├── browse.php
│ │ │ ├── detail.php
│ │ │ └── checkout.php
│ │ ├── user/
│ │ │ ├── dashboard.php
│ │ │ └── orders.php
│ │ └── admin/
│ │ ├── dashboard.php
│ │ ├── users.php
│ │ ├── boxes.php
│ │ └── orders.php
│ ├── Helpers/
│ │ ├── AuthHelper.php
│ │ ├── ValidationHelper.php
│ │ └── Utils.php
│
├── database/
│ ├── schema.sql
│ └── seeders.sql
│
├── docs/
│ ├── ER_Diagram.vpp
│ ├── Use_Case.vpp
│ ├── Class_Diagram.vpp
│ ├── Sequence_Diagram.vpp
│ └── README.md
│
├── composer.json
├── tailwind.config.js
└── README.md


