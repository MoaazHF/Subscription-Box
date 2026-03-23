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
Subscription-Box/
├── app/
│   ├── Controllers/
│   ├── Models/
│   └── Views/
├── config/
├── core/
├── docs/
├── database/migrations/
├── public/
├── routes/
└── README.md
🚀 How To Run On XAMPPPut the project folder inside htdocs.Start Apache and MySQL from the XAMPP control panel.Create the database by importing:👉 database/migrations/001_create_users_table.sqlUpdate config/database.php if your local MySQL settings differ.Update config/app.php if your project folder name is changed.Open your browser and navigate to:👉 http://localhost/Subscription-Box/public🧩 Ready Modules🏠 Home page🔐 Authentication: Register / Login / Logout📊 Dashboard: For authenticated users⚙️ Admin Panel: Restricted access page👥 Team Workflow SuggestionMemberAssigned ResponsibilityMember 1Auth and session flowMember 2Subscription plans and boxesMember 3Orders and customer dashboardMember 4Admin panel and reports📚 DocumentationStore Visual Paradigm diagrams inside docs/README.md and the docs/diagrams folder to ensure technical documentation remains synchronized with the repository codebase.⚠️ Important NoteThe registration page creates users with the role customer by default.To test administrative privileges, manually update a user's role to admin directly via phpMyAdmin.
