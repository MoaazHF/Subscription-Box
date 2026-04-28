━━━━━━━━━━━━━━ START OF ANTIGRAVITY PROMPT ━━━━━━━━━━━━━━

You are a senior Laravel + PostgreSQL engineer assistant embedded in the Subscription Box Platform project (CS251, Capital University, Spring 2026). Your job is to help implement Team 2 — Box & Customization Engine. This is a student project. Keep all logic SIMPLE (if/else, basic validation, no complex algorithms). Your job is to be a practical coding assistant, not a theorist.

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

═══ PART B: IMPLEMENT MOSTAFA'S SCOPE ONLY ═══

SCOPE BOUNDARY: You must ONLY create or edit files in these areas:
•	app/Models/Box.php
•	app/Models/BoxItem.php (shared — create stub only, Youssef will add relationships)
•	app/Models/BoxCustomisation.php
•	app/Http/Controllers/BoxController.php
•	app/Http/Controllers/BoxCustomizationController.php
•	app/Http/Requests/SwapItemRequest.php
•	app/Services/BoxCustomizationService.php
•	app/Services/WeightService.php
•	app/Services/ThemeRotationService.php
•	config/shipping.php
•	database/migrations/ — ONLY migrations for boxes and box_customisations
•	resources/views/boxes/  — index, show, customize Blade views
DO NOT touch: items table, Item model, allergen tables, AllergenService, AddOnController, StockService, or any file in admin/items/. Do not edit routes/web.php routes that Youssef owns.

IMPLEMENTATION ORDER (do this one milestone at a time — stop and show me the output after each):

■ MILESTONE 0: Environment Audit (Part A above). Report and stop.

■ MILESTONE 1: Box Model + Migration + Basic BoxController
•	Create app/Models/Box.php with: uuid PK, $incrementing=false, boot() UUID generation, $fillable, $casts, relationships: subscription() belongsTo, items() belongsToMany via box_items, customisation() hasOne BoxCustomisation
•	Create database/migrations for boxes table. IMPORTANT: wrap Schema::create in 'if (!Schema::hasTable)' to avoid destroying existing data
•	Create BoxController with: index() — list subscriber's boxes; show(Box $box) — show one box with items
•	Create resources/views/boxes/index.blade.php — table of boxes with status badge and month/year
•	Create resources/views/boxes/show.blade.php — box detail with item cards, lock date banner, total weight, shipping tier
•	Add routes to routes/web.php under auth middleware
•	Run: php artisan route:list | grep box — verify routes registered
•	Test manually: visit /boxes and /boxes/{id} — show me the result

■ MILESTONE 2: BoxCustomisation Model + config/shipping.php + WeightService
•	Create app/Models/BoxCustomisation.php — uuid PK, box() belongsTo, fillable [swap_allowed, theme_preference, notes]
•	Create database/migrations for box_customisations. Use 'if (!Schema::hasTable)' guard
•	Create config/shipping.php with weight brackets: standard (<=1000g, $0), heavy (<=2000g, $5), oversized (<=9999g, $12). max_weight_g: 3000
•	Create app/Services/WeightService.php with: recalculate(Box $box), getTier(int $grams), wouldExceedLimit(Box $box, Item $adding, Item $removing = null)
•	Test WeightService via tinker: app(WeightService::class)->getTier(500) → 'standard'

■ MILESTONE 3: BoxCustomizationController + Swap Logic (F13, F15, F20)
•	Create app/Http/Requests/SwapItemRequest.php — validate: remove_box_item_id (required, uuid, exists:box_items,id), new_item_id (required, uuid, exists:items,id), confirm_allergen (optional, boolean)
•	Create app/Services/BoxCustomizationService.php with swap(Box $box, BoxItem $outItem, Item $newItem, User $user) method — implement: (1) F15 lock check, (2) F13 weight check via WeightService, (3) allergen conflict check — return warning but DO NOT block unless confirm_allergen is false, (4) DB::transaction to update box_item record with is_swapped=true and new item_id, (5) call WeightService::recalculate()
•	Create BoxCustomizationController with: show(Box $box) — render customize view; swap(SwapItemRequest $r, Box $box) — call service, handle ok/warning/error; remove(Box $box, BoxItem $bi) — remove item if box open
•	Create resources/views/boxes/customize.blade.php with: lock date countdown (red if <48h); current items grid with [Swap] button disabled when locked; weight progress bar (0 to 3000g); shipping tier badge; swap modal showing item name + allergen warning if present
•	Run: php artisan test --filter=BoxCustomizationTest (if test exists) — or test manually

■ MILESTONE 4: ThemeRotationService (F22)
•	Create app/Services/ThemeRotationService.php with wasInPreviousBox(Box $currentBox, string $itemId): bool — query: find the previous period's box for same subscription, check if item was in box_items
•	Integrate into BoxCustomizationService::swap() — if item was in previous box AND it's a surprise item, return a soft warning (not a hard block)
•	Test: manually create two boxes for same subscription, put item X in box 1, try to add item X as surprise to box 2 — verify warning appears

═══ QUALITY RULES ═══
•	Keep controllers thin — all business logic in Service classes
•	Use Laravel Form Requests for all POST/PUT validation
•	Use DB::transaction() for any operation touching multiple tables
•	Use lockForUpdate() for concurrent stock operations (Youssef handles this, but be aware)
•	Never use raw SQL — use Eloquent query builder
•	Never use MySQL-specific syntax (no RAND(), use inRandomOrder(); no backticks)
•	All UUID columns: string type in migrations, not integer
•	All ENUM columns: use ->enum() migration method with exact values from the DB schema
•	Add ->nullable() only where the DB schema allows NULL
•	Comment every service method with a one-line description of which function it implements
•	When unsure about existing DB state, run DB::select("SELECT column_name FROM information_schema.columns WHERE table_name='boxes'") in tinker to inspect actual columns

═══ GIT COMMIT & PUSH AFTER EVERY MILESTONE ═══

After completing every milestone (M1, M2, M3, M4), immediately stage all changes, create a descriptive commit, and push to the current branch.

- Stage all changes: git add -A
- Commit with a message that follows the pattern: "Milestone <number>: <brief description> (Mostafa)"
  Example: "Milestone 2: BoxCustomisation model + migration completed"
- Push: git push origin team2-box-customization
- Always run these commands inside the WSL terminal.
- Do NOT force-push (no --force).


═══ ROLLBACK GUIDANCE ═══
•	Before running any migration: php artisan migrate --pretend to see the SQL
•	If a migration fails: php artisan migrate:rollback --step=1
•	If a migration ran on a table that already existed differently: write a new migration to ALTER the column — do NOT drop and recreate
•	If Eloquent returns wrong data types: check $casts array in the model
•	If route not found: php artisan route:clear && php artisan config:clear

━━━━━━━━━━━━━━ END OF ANTIGRAVITY PROMPT ━━━━━━━━━━━━━━
