# Subscription Box Portal

Simple course-friendly template built with native PHP MVC, PDO, MySQL, Tailwind CSS, and XAMPP.

## Stack

- Backend: Native PHP with manual MVC
- Database: MySQL + PDO
- Frontend: HTML + Tailwind CSS
- Authentication: `$_SESSION` + `password_hash()` + `password_verify()`
- Authorization: Manual role checks inside controllers
- Design Pattern: Singleton `DatabaseManager`

## Project Structure

```text
Subscription-Box/
|-- app/
|   |-- Controllers/
|   |-- Models/
|   `-- Views/
|-- config/
|-- core/
|-- docs/
|-- database/migrations/
|-- public/
|-- routes/
`-- README.md
```

## How To Run On XAMPP

1. Put the project folder inside `htdocs`.
2. Start `Apache` and `MySQL` from XAMPP.
3. Create the database by importing [database/migrations/001_create_users_table.sql](/d:/FCAI/Collage/2/Term%202/Software%20Engneering/Project/Subscription-Box/database/migrations/001_create_users_table.sql).
4. Update [config/database.php](/d:/FCAI/Collage/2/Term%202/Software%20Engneering/Project/Subscription-Box/config/database.php) if your MySQL settings are different.
5. Update [config/app.php](/d:/FCAI/Collage/2/Term%202/Software%20Engneering/Project/Subscription-Box/config/app.php) if your project folder name changes.
6. Open `http://localhost/Subscription-Box/public`.

## Ready Modules

- Home page
- Register / Login / Logout
- Dashboard for authenticated users
- Admin-only users page

## Team Workflow Suggestion

- Member 1: Auth and session flow
- Member 2: Subscription plans and boxes
- Member 3: Orders and customer dashboard
- Member 4: Admin panel and reports

## Documentation

Store Visual Paradigm diagrams inside [docs/README.md](/d:/FCAI/Collage/2/Term%202/Software%20Engneering/Project/Subscription-Box/docs/README.md) and the `docs/diagrams` folder so the technical documentation stays beside the code.

## Important Note

The registration page creates users with role `customer` by default. If you want an admin account for testing, update the user's `role` to `admin` manually from phpMyAdmin.
