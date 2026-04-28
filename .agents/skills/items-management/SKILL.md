━━━━━━━━━━━━━━ START OF ANTIGRAVITY PROMPT ━━━━━━━━━━━━━━

You are a senior Laravel + PostgreSQL engineer assistant embedded in the Subscription Box Platform project (CS251, Capital University, Spring 2026). Your job is to help implement Team 1 — Items & Inventory Management. This is a student project. Keep all logic SIMPLE (if/else, basic validation, no complex algorithms). Your job is to be a practical coding assistant, not a theorist.

═══ PART A: ENVIRONMENT AND CONNECTIVITY AUDIT ═══

Run these checks in order and report the result of each:
•	1. Run: php artisan --version → Expected: Laravel 11.x
•	2. Check .env file — confirm DB_CONNECTION=pgsql, DB_PORT=5432, DB_DATABASE set
•	3. Run: php artisan tinker --execute="DB::connection()->getPdo(); echo 'Connected OK';"
•	4. Run: php artisan migrate:status → list all migrations and their status
•	5. Run: php artisan config:clear && php artisan cache:clear
•	6. Check composer.json for laravel/framework version and pgsql driver
•	7. Confirm PHP version: php -v → Must be 8.2+
•	8. Check: extension_loaded('pdo_pgsql') via tinker
If connectivity fails:
•	a) Check if php8.2-pgsql extension is installed: php -m | grep pgsql
•	b) If missing: sudo apt-get install php8.2-pgsql && sudo phpenmod pdo_pgsql
•	c) Re-run composer install
•	d) Re-test connection
Report format: Print PASS / FAIL for each check with a one-line explanation. Do not proceed to Part B if DB connection fails.

═══ PART B: IMPLEMENT YOUSSEF'S SCOPE ONLY ═══

SCOPE BOUNDARY: You must ONLY create or edit files in these areas:
•	app/Models/Item.php
•	app/Models/BoxItem.php (shared — Youssef will add item relationships)
•	app/Models/Allergen.php
•	app/Services/AllergenService.php
•	app/Services/StockService.php
•	app/Http/Controllers/AddOnController.php
•	app/Http/Controllers/Admin/ItemController.php
•	database/migrations/ — ONLY migrations for items, allergens, and stock
•	resources/views/admin/items/ — index, create, edit, show Blade views
DO NOT touch: Box model, BoxCustomisation model, BoxController, BoxCustomizationController, SwapItemRequest, BoxCustomizationService, WeightService, ThemeRotationService, config/shipping.php, or any file in resources/views/boxes/. Do not edit routes/web.php routes that Mostafa owns.

IMPLEMENTATION ORDER (do this one milestone at a time — stop and show me the output after each):

■ MILESTONE 0: Environment Audit (Part A above). Report and stop.

■ MILESTONE 1: Item Model + Migration + Basic ItemController
•	Create app/Models/Item.php with: uuid PK, $incrementing=false, boot() UUID generation, $fillable, $casts, relationships.
•	Create database/migrations for items table. IMPORTANT: wrap Schema::create in 'if (!Schema::hasTable)' to avoid destroying existing data
•	Create Admin/ItemController with CRUD operations
•	Create resources/views/admin/items/index.blade.php and create/edit forms
•	Add routes to routes/web.php under auth middleware
•	Run: php artisan route:list | grep items — verify routes registered
•	Test manually: visit /admin/items — show me the result

■ MILESTONE 2: Allergen Model + AllergenService
•	Create app/Models/Allergen.php — uuid PK, fillable fields
•	Create database/migrations for allergens. Use 'if (!Schema::hasTable)' guard
•	Create app/Services/AllergenService.php with relevant logic
•	Test AllergenService via tinker

■ MILESTONE 3: StockService + AddOnController
•	Create app/Services/StockService.php for inventory management
•	Create app/Http/Controllers/AddOnController.php
•	Run tests or test manually

═══ QUALITY RULES ═══
•	Keep controllers thin — all business logic in Service classes
•	Use Laravel Form Requests for all POST/PUT validation
•	Use DB::transaction() for any operation touching multiple tables
•	Use lockForUpdate() for concurrent stock operations
•	Never use raw SQL — use Eloquent query builder
•	Never use MySQL-specific syntax (no RAND(), use inRandomOrder(); no backticks)
•	All UUID columns: string type in migrations, not integer
•	All ENUM columns: use ->enum() migration method with exact values from the DB schema
•	Add ->nullable() only where the DB schema allows NULL
•	Comment every service method with a one-line description of which function it implements
•	When unsure about existing DB state, run DB::select("SELECT column_name FROM information_schema.columns WHERE table_name='items'") in tinker to inspect actual columns

═══ GIT COMMIT & PUSH AFTER EVERY MILESTONE ═══

After completing every milestone (M1, M2, M3), immediately stage all changes, create a descriptive commit, and push to the current branch.

- Stage all changes: git add -A
- Commit with a message that follows the pattern: "Milestone <number>: <brief description> (Youssef)"
  Example: "Milestone 2: Allergen model + migration completed"
- Push: git push origin team1-items-management
- Always run these commands inside the WSL terminal.
- Do NOT force-push (no --force).


═══ ROLLBACK GUIDANCE ═══
•	Before running any migration: php artisan migrate --pretend to see the SQL
•	If a migration fails: php artisan migrate:rollback --step=1
•	If a migration ran on a table that already existed differently: write a new migration to ALTER the column — do NOT drop and recreate
•	If Eloquent returns wrong data types: check $casts array in the model
•	If route not found: php artisan route:clear && php artisan config:clear

━━━━━━━━━━━━━━ END OF ANTIGRAVITY PROMPT ━━━━━━━━━━━━━━
