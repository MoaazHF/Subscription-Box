# SYSTEM READY: ANALYZING PROVIDED CODEBASE.

## Phase 1: Stack Lexicon

| Construct | Category | Definition | Role in This Codebase | Minimal Example |
| --- | --- | --- | --- | --- |
| `namespace` | PHP | Declares the logical package of a class file. | Separates controllers, models, requests, and services into framework-resolvable domains. | `namespace App\Models;` |
| `use` | PHP | Imports classes into the current namespace. | Reduces fully qualified type names and enables DI signatures. | `use App\Services\SubscriptionService;` |
| `class` | PHP | Defines an instantiable type. | Every controller, model, request, middleware, and service is a class. | `class SubscriptionController extends Controller` |
| `extends` | PHP/OOP | Inherits behavior from a parent class. | Controllers inherit base Laravel HTTP behavior; requests inherit validation behavior. | `class LoginRequest extends FormRequest` |
| `implements` | PHP/OOP | Commits a class to a contract. | Not heavily used here; the architecture relies more on inheritance and framework base classes. | `class Example implements ShouldQueue` |
| `trait` | PHP | Reusable method bundle mixed into a class. | `HasFactory`, `Notifiable`, and `HasUuids` add framework behaviors to models. | `use HasUuids;` |
| `return type` | PHP | Declares the output type of a method. | Makes controller/service/model APIs explicit and predictable. | `public function index(): View` |
| `constructor property promotion` | PHP 8 | Declares and assigns constructor dependencies in one place. | Controllers and services receive collaborators through DI with minimal boilerplate. | `public function __construct(private AuditLogService $auditLogService) {}` |
| `?->` | PHP 8 | Nullsafe operator that reads a member only if the left side is non-null. | Used in views to avoid crashes when optional relations are absent. | `$payment->created_at?->format('M d, Y')` |
| `match` | PHP 8 | Expression-based branch selection. | Used in service/config style decisions where compact branching is needed. | `match ($tier) { ... }` |
| `closure` | PHP | Anonymous function object. | Defines route handlers and the scheduled renewal command. | `Artisan::command('subscriptions:renew', function () { ... });` |
| `FormRequest` | Laravel | Validation + authorization object bound to a route action. | Centralizes request rules for login, registration, address CRUD, and subscriptions. | `public function store(StoreSubscriptionRequest $request)` |
| `validated()` | Laravel | Returns only data that passed validation. | Prevents accidental use of untrusted input in persistence flows. | `$data = $request->validated();` |
| `middleware` | Laravel | Request filter that runs before/after the controller. | Protects routes with `auth`, `guest`, and custom role checks. | `Route::middleware('auth')->group(...)` |
| `Route::middleware()->group()` | Laravel Routing | Applies shared filters to multiple routes. | Separates guest flows from authenticated flows and admin-only flows. | `Route::middleware('guest')->group(...)` |
| `route()` | Laravel Routing | Generates a URL from a named route. | Makes Blade forms and links stable against path refactors. | `route('subscriptions.store')` |
| `redirect()->route()` | Laravel HTTP | Redirect response targeting a named route. | Used after mutations to keep browser navigation consistent. | `return redirect()->route('dashboard');` |
| `view()` | Laravel Views | Builds a view response. | Renders dashboard, plans, addresses, payments, audit logs, and box screens. | `return view('plans.index', compact('plans'));` |
| `compact()` | PHP/Laravel | Builds an array from variable names. | Keeps controller view payload assembly short. | `compact('payments')` |
| `Eloquent Model` | Laravel ORM | Active Record wrapper around a database table. | Encodes relations, casting, UUID behavior, and write rules. | `class Subscription extends Model` |
| `belongsTo()` | Eloquent | Inverse one-to-many relation. | Payments belong to subscriptions; subscriptions belong to users, plans, and addresses. | `return $this->belongsTo(User::class);` |
| `hasMany()` | Eloquent | One-to-many relation. | Users own addresses, subscriptions, and audit logs. | `return $this->hasMany(Address::class);` |
| `belongsToMany()` | Eloquent | Many-to-many relation through a pivot table. | Boxes and items connect through `box_items`. | `return $this->belongsToMany(Item::class, 'box_items')` |
| `with()` | Eloquent | Eager-loads relations to avoid lazy query bursts. | Controllers use it to load plans, addresses, roles, payments, and users up front. | `Subscription::with(['plan', 'address'])` |
| `whereBelongsTo()` | Eloquent | Filters rows by a related model instance. | Dashboard/payment queries bind directly to the authenticated user. | `Subscription::whereBelongsTo($user)` |
| `transaction()` | DB | Executes a closure atomically. | Subscription creation, plan change, and renewals treat multi-write operations as a unit. | `DB::transaction(function () { ... });` |
| `upsert()` | Query Builder | Insert-or-update based on unique keys. | Seeder keeps roles and plans idempotent across reruns. | `DB::table('roles')->upsert([...], ['name'], ['name']);` |
| `casts()` | Eloquent | Declares runtime type conversions for model attributes. | Converts dates, booleans, decimals, arrays, and JSON to usable PHP types. | `'features' => 'array'` |
| `#[Fillable]` | Laravel Attribute API | Declares mass-assignable attributes through PHP attributes. | User model follows newer Laravel attribute-based configuration. | `#[Fillable(['name', 'email'])]` |
| `HasUuids` | Eloquent Trait | Auto-generates UUID primary keys. | Core business entities use UUIDs to avoid sequential ID exposure. | `use HasUuids;` |
| `abort_if()` | Laravel Helper | Throws an HTTP exception if a condition is true. | Enforces ownership and business state constraints. | `abort_if(! $subscription->user->is($user), 403);` |
| `session()` | Laravel Session | Reads flashed or persistent session state. | Blade surfaces alerts and swap warnings from previous requests. | `session('success')` |
| `Auth::attempt()` | Laravel Auth | Tries session login with credentials. | Implements manual email/password login in `AuthController`. | `Auth::attempt($credentials)` |
| `Auth::logout()` | Laravel Auth | Ends the authenticated session. | Terminates user access on logout. | `Auth::logout();` |
| `@extends` | Blade | Inherits a layout template. | Most Team 1 views share `layouts.app`. | `@extends('layouts.app')` |
| `@section` | Blade | Fills a named layout slot. | Injects screen-specific content into the global shell. | `@section('content')` |
| `@csrf` | Blade | Injects a CSRF token field into forms. | Protects all state-changing forms. | `@csrf` |
| `@method` | Blade | Spoofs HTTP verbs not supported by native forms. | Enables `PUT` and `DELETE` forms. | `@method('DELETE')` |
| `@forelse` | Blade | Loops with an empty fallback branch. | Used for payments, audit logs, and some resource lists. | `@forelse ($payments as $payment)` |
| `@if` | Blade | Conditional rendering directive. | Drives auth state, lock banners, swap warnings, and validation feedback. | `@if (session('error'))` |
| `@auth` / `@guest` | Blade | Conditional rendering based on session auth state. | Adjusts navigation and public/private page controls. | `@auth ... @endauth` |
| `@disabled` / raw `disabled` | Blade/HTML | Prevents interaction when a condition fails. | Used for locked boxes and flow gating. | `{{ $isLocked ? 'disabled' : '' }}` |
| `@vite` | Laravel Frontend | Loads compiled frontend assets. | Connects Blade screens to Tailwind/CSS and app JS. | `@vite(['resources/css/app.css'])` |
| `pagination links` | Laravel Views | Renders paginator navigation. | Payments and audit logs are bounded rather than fully hydrated. | `{{ $payments->links() }}` |
| `Tailwind utility classes` | Tailwind CSS | Atomic CSS classes attached directly in markup. | Drive spacing, typography, cards, tables, forms, alerts, and layout without separate CSS files. | `rounded-[2rem] border border-stone-200 bg-white p-8` |
| `x-data` / `x-show` | Alpine.js | Lightweight frontend state and conditional display directives. | Used only in box customization modal flow. | `x-data="{ swapModalOpen: false }"` |

## Phase 2: Macro Architecture

### 2.1 Custom Directory Tree

```text
app/
├── Http/
│   ├── Controllers/
│   │   ├── AddressController.php
│   │   ├── AuditLogController.php
│   │   ├── AuthController.php
│   │   ├── BoxController.php
│   │   ├── BoxCustomizationController.php
│   │   ├── Controller.php
│   │   ├── DashboardController.php
│   │   ├── HomeController.php
│   │   ├── PaymentController.php
│   │   ├── SubscriptionController.php
│   │   └── SubscriptionPlanController.php
│   ├── Middleware/
│   │   └── RoleMiddleware.php
│   └── Requests/
│       ├── AddressRequest.php
│       ├── LoginRequest.php
│       ├── RegisterUserRequest.php
│       ├── StoreSubscriptionRequest.php
│       ├── SwapItemRequest.php
│       └── UpdateSubscriptionPlanRequest.php
├── Models/
│   ├── Address.php
│   ├── AuditLog.php
│   ├── Box.php
│   ├── BoxCustomisation.php
│   ├── BoxItem.php
│   ├── Item.php
│   ├── Payment.php
│   ├── Role.php
│   ├── Subscription.php
│   ├── SubscriptionPlan.php
│   └── User.php
├── Providers/
│   └── AppServiceProvider.php
└── Services/
    ├── AuditLogService.php
    ├── BillingService.php
    ├── BoxCustomizationService.php
    ├── SubscriptionService.php
    ├── TaxService.php
    ├── ThemeRotationService.php
    └── WeightService.php
bootstrap/
└── app.php
config/
├── auth.php
├── cache.php
├── queue.php
├── session.php
├── shipping.php
└── subscriptions.php
database/
├── factories/
│   └── UserFactory.php
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 0001_01_01_000001_create_cache_table.php
│   ├── 0001_01_01_000002_create_jobs_table.php
│   ├── 2026_04_26_035700_create_subscription_platform_schema.php
│   ├── 2026_04_26_035736_create_boxes_table.php
│   └── 2026_04_26_040242_create_box_customisations_table.php
└── seeders/
    └── DatabaseSeeder.php
resources/
└── views/
    ├── addresses/index.blade.php
    ├── audit-logs/index.blade.php
    ├── auth/login.blade.php
    ├── auth/register.blade.php
    ├── boxes/customize.blade.php
    ├── boxes/index.blade.php
    ├── boxes/show.blade.php
    ├── dashboard.blade.php
    ├── layouts/app.blade.php
    ├── payments/index.blade.php
    ├── plans/index.blade.php
    ├── subscriptions/index.blade.php
    └── welcome.blade.php
routes/
├── console.php
└── web.php
```

| File | Job |
| --- | --- |
| `app/Http/Controllers/AddressController.php` | Owns authenticated address CRUD and default-address normalization for the current user. |
| `app/Http/Controllers/AuditLogController.php` | Exposes paginated audit history to admins only. |
| `app/Http/Controllers/AuthController.php` | Implements manual register, login, and logout flows on top of Laravel session auth. |
| `app/Http/Controllers/BoxController.php` | Lists and shows boxes while enforcing owner-or-admin visibility. |
| `app/Http/Controllers/BoxCustomizationController.php` | Coordinates swap and remove actions for box contents through a service layer. |
| `app/Http/Controllers/Controller.php` | Serves as the conventional Laravel base controller with no custom behavior. |
| `app/Http/Controllers/DashboardController.php` | Builds the authenticated landing screen from user, address, subscription, and payment data. |
| `app/Http/Controllers/HomeController.php` | Builds the public landing page with active subscription plans. |
| `app/Http/Controllers/PaymentController.php` | Shows the current user’s payment history with pagination. |
| `app/Http/Controllers/SubscriptionController.php` | Handles subscription creation, pause/resume, plan changes, and access control. |
| `app/Http/Controllers/SubscriptionPlanController.php` | Displays active public plans for browsing before purchase. |
| `app/Http/Middleware/RoleMiddleware.php` | Rejects authenticated users whose role name is not in the allowed route list. |
| `app/Http/Requests/AddressRequest.php` | Validates address fields and exposes a normalized input contract to the controller. |
| `app/Http/Requests/LoginRequest.php` | Validates email/password login credentials. |
| `app/Http/Requests/RegisterUserRequest.php` | Validates new-account creation fields including password confirmation and unique email. |
| `app/Http/Requests/StoreSubscriptionRequest.php` | Validates subscription creation payload including selected plan, address, and flags. |
| `app/Http/Requests/SwapItemRequest.php` | Validates box item swap requests and confirmation flags. |
| `app/Http/Requests/UpdateSubscriptionPlanRequest.php` | Validates a requested replacement plan for upgrade or downgrade. |
| `app/Models/Address.php` | Represents a user-owned delivery address with UUID keys and subscription linkage. |
| `app/Models/AuditLog.php` | Represents immutable audit entries with JSON metadata and optional user linkage. |
| `app/Models/Box.php` | Represents a monthly subscription box with ownership helper methods and item/customization relations. |
| `app/Models/BoxCustomisation.php` | Represents a one-to-one customization record for a box. |
| `app/Models/BoxItem.php` | Represents a pivot-like record connecting boxes to items with extra state fields. |
| `app/Models/Item.php` | Represents catalog items that can be inserted into boxes. |
| `app/Models/Payment.php` | Represents simulated billing transactions tied to subscriptions. |
| `app/Models/Role.php` | Represents RBAC role records and centralizes role-name constants. |
| `app/Models/Subscription.php` | Represents the core recurring customer contract with plan, address, billing, and box relations. |
| `app/Models/SubscriptionPlan.php` | Represents sellable plan definitions including price and feature metadata. |
| `app/Models/User.php` | Represents authenticatable users with UUID IDs, role linkage, and Team 1 relations. |
| `app/Providers/AppServiceProvider.php` | Remains effectively empty and contributes no custom application boot logic. |
| `app/Services/AuditLogService.php` | Persists readable audit entries from controllers and subscription workflows. |
| `app/Services/BillingService.php` | Creates simulated payment records from a plan and billing reason. |
| `app/Services/BoxCustomizationService.php` | Encapsulates box swap/remove rules including lock, weight, allergen, and rotation checks. |
| `app/Services/SubscriptionService.php` | Encapsulates subscription lifecycle state transitions and renewal behavior. |
| `app/Services/TaxService.php` | Computes a simple percentage-based tax from configuration. |
| `app/Services/ThemeRotationService.php` | Detects whether a candidate item repeated from a previous box period. |
| `app/Services/WeightService.php` | Computes shipping tier and enforces configurable box weight thresholds. |
| `bootstrap/app.php` | Registers application middleware aliases and finalizes the HTTP kernel bootstrap. |
| `config/auth.php` | Configures the session guard and Eloquent user provider used by login flows. |
| `config/cache.php` | Configures a database-backed cache store that must exist for runtime stability. |
| `config/queue.php` | Configures a database-backed queue connection that depends on migrated jobs tables. |
| `config/session.php` | Configures a database-backed session driver that depends on migrated sessions support. |
| `config/shipping.php` | Defines box weight ceilings and shipping tier thresholds used by box services. |
| `config/subscriptions.php` | Defines the tax rate and billing-cycle length used by billing and renewal logic. |
| `database/factories/UserFactory.php` | Creates test users while auto-provisioning the subscriber role if missing. |
| `database/migrations/0001_01_01_000000_create_users_table.php` | Creates UUID-based users and session tables required for auth and session persistence. |
| `database/migrations/0001_01_01_000001_create_cache_table.php` | Creates the cache tables required by the database cache driver. |
| `database/migrations/0001_01_01_000002_create_jobs_table.php` | Creates queue tables required by the database queue driver. |
| `database/migrations/2026_04_26_035700_create_subscription_platform_schema.php` | Creates the full business schema and seeds foundational reference data through raw PostgreSQL DDL. |
| `database/migrations/2026_04_26_035736_create_boxes_table.php` | Adds an idempotent fallback box migration for environments missing the main schema migration. |
| `database/migrations/2026_04_26_040242_create_box_customisations_table.php` | Adds an idempotent fallback customization migration for environments missing the main schema migration. |
| `database/seeders/DatabaseSeeder.php` | Seeds canonical roles, plans, and two demonstrator users in an idempotent way. |
| `resources/views/addresses/index.blade.php` | Renders address creation, inline editing, default selection, and deletion. |
| `resources/views/audit-logs/index.blade.php` | Renders plain, review-friendly audit entries for admins. |
| `resources/views/auth/login.blade.php` | Renders the session login form and seeded demo credentials. |
| `resources/views/auth/register.blade.php` | Renders the manual registration form. |
| `resources/views/boxes/customize.blade.php` | Renders the only JavaScript-heavy screen, including swap modal state and lock feedback. |
| `resources/views/boxes/index.blade.php` | Renders the user-visible list of boxes with status and navigation. |
| `resources/views/boxes/show.blade.php` | Renders a single box summary, lock state, and current items. |
| `resources/views/dashboard.blade.php` | Renders the authenticated summary page and Team 1 quick actions. |
| `resources/views/layouts/app.blade.php` | Defines the shared shell, navigation, flash surfaces, and asset loading for most screens. |
| `resources/views/payments/index.blade.php` | Renders paginated simulated payment history. |
| `resources/views/plans/index.blade.php` | Renders active public subscription plans. |
| `resources/views/subscriptions/index.blade.php` | Renders subscription creation and lifecycle controls for the current user. |
| `resources/views/welcome.blade.php` | Renders the marketing-oriented public landing page with plan highlights. |
| `routes/console.php` | Declares and schedules the daily renewal command. |
| `routes/web.php` | Declares the entire public, guest, authenticated, admin, and box HTTP surface. |

### 2.2 Request Lifecycle Mapping

#### Flow A: Guest Registration/Login

`Browser GET /register -> guest middleware -> AuthController@showRegistrationForm -> auth/register.blade.php`

`Browser POST /register -> guest middleware -> RegisterUserRequest -> AuthController@register -> User/Role lookup + creation -> AuditLogService -> Auth::login() -> redirect dashboard`

`Browser POST /login -> guest middleware -> LoginRequest -> AuthController@login -> Auth::attempt() session guard -> session regeneration -> AuditLogService -> redirect dashboard`

Operational consequence:
- Validation terminates bad payloads before any persistence logic runs.
- Role assignment is hard-coded to `subscriber`, which keeps onboarding simple but makes multi-role registration impossible by design.
- Audit logging is synchronous, so auth flow latency includes one extra write.

#### Flow B: Authenticated Subscription Creation

`Browser GET /subscriptions -> auth middleware -> SubscriptionController@index -> eager load plans, user addresses, user subscriptions -> subscriptions/index.blade.php`

`Browser POST /subscriptions -> auth middleware -> StoreSubscriptionRequest -> SubscriptionController@store -> SubscriptionService@createForUser -> DB transaction -> subscriptions insert + payments insert + audit_logs insert -> redirect subscriptions index`

Operational consequence:
- The controller enforces “one active or paused subscription at a time” before service invocation.
- The service treats billing as part of subscription creation, so a subscription cannot exist without an initial payment row.
- The database is the system of record; no external payment gateway exists yet.

#### Flow C: Admin Audit Log Access

`Browser GET /audit-logs -> auth middleware -> role:admin middleware -> AuditLogController@index -> AuditLog::with('user')->latest()->paginate(20) -> audit-logs/index.blade.php`

Operational consequence:
- Authorization is route-level rather than policy-level.
- View rendering uses eager-loaded users to avoid one query per log entry.
- Logs are read-only through the UI; mutation is append-only through services/controllers.

### 2.3 Data Model Summary

| Relationship | Cardinality | Meaning |
| --- | --- | --- |
| `roles -> users` | one-to-many | One role can be assigned to many users; each user has exactly one role. |
| `users -> addresses` | one-to-many | One user can store multiple addresses; each address belongs to one user. |
| `users -> subscriptions` | one-to-many | One user can own multiple subscriptions historically, but controller logic limits concurrent active/paused ones. |
| `subscription_plans -> subscriptions` | one-to-many | One plan can back many subscriptions; each subscription selects one plan. |
| `addresses -> subscriptions` | one-to-many | One address can be reused by multiple subscriptions; each subscription points to one address or null. |
| `subscriptions -> payments` | one-to-many | Every billing event generates a payment row tied to one subscription. |
| `users -> audit_logs` | one-to-many | One user can cause many audit entries; a log may also be system-generated with no user. |
| `subscriptions -> boxes` | one-to-many | A subscription can produce one box per period; boxes are period-unique per subscription. |
| `boxes -> box_customisations` | one-to-one | A box may have one customization record. |
| `boxes <-> items` | many-to-many via `box_items` | A box contains many items and an item may appear in many boxes. |
| `users <-> allergen_tags` | many-to-many via `user_allergens` | A user can declare many allergens and an allergen can apply to many users. |
| `items <-> allergen_tags` | many-to-many via `item_allergens` | An item can carry many allergens and an allergen can label many items. |

Architectural boundary:
- Team 1 foundation is `roles`, `users`, `addresses`, `subscription_plans`, `subscriptions`, `payments`, `audit_logs`.
- Box customization is downstream logic that depends on subscription existence and ownership rules, but it has already been partially integrated into the same codebase.

## Phase 3: File-by-File Technical Autopsy

### 3.1 Bootstrap and Routing

#### `bootstrap/app.php`

Logical blocks:
- Application bootstrap chain.
- Middleware alias registration.

Analysis:
- The bootstrap file stays close to Laravel defaults, which is good because framework bootstrap is not the place for business logic.
- The only meaningful custom line is the alias registration for `role`, which maps route middleware strings to `RoleMiddleware`.
- This design keeps role checks declarative in `routes/web.php` instead of hard-coding guards in every controller.
- Global state impact: route resolution can now interpret `role:admin` and pass parameters into custom middleware.

#### `routes/web.php`

Logical blocks:
- Public routes.
- Guest auth routes.
- Authenticated app routes.
- Admin audit route group.
- Box routes.

Analysis:
- Public `GET /` and `GET /plans` expose only read-only surfaces, which correctly separates anonymous browsing from account state mutation.
- Guest group prevents authenticated users from seeing login/register screens, which avoids confused auth state and session edge cases.
- Auth group centralizes all stateful application routes under `auth`, reducing accidental exposure of user-owned resources.
- Audit logs receive an additional `role:admin` guard at route level, which is adequate for a student codebase though less flexible than policies.
- Box routes are grouped under `auth`, then ownership is rechecked in controllers; this is defense in depth rather than reliance on routing alone.
- State effect: this file defines the entire HTTP boundary, so route names here become the stable API used by all Blade forms and redirects.

#### `routes/console.php`

Logical blocks:
- Renewal command definition.
- Daily schedule declaration.

Analysis:
- `Artisan::command('subscriptions:renew', ...)` inlines the task rather than generating a dedicated command class; this is simple but less extensible.
- The closure resolves `SubscriptionService` from the container and delegates real logic to the service, which is the correct layering choice.
- `->dailyAt('01:00')` encodes a deterministic renewal cadence without spreading schedule semantics into controllers.
- State effect: due subscriptions can be renewed without a browser request, which is the first real background automation in the project.

### 3.2 Controllers

#### `app/Http/Controllers/Controller.php`

Analysis:
- This file is intentionally empty.
- Its existence is structural, not behavioral.
- No state changes originate here.

#### `AuthController.php`

Logical blocks:
- Screen rendering.
- Registration.
- Login.
- Logout.

Analysis:
- The rendering methods return plain views and deliberately contain no branching, which keeps GET auth endpoints trivial.
- Registration validates through `RegisterUserRequest`, resolves the subscriber role, and creates a user with hashed password, preventing the controller from handling raw password storage itself.
- The controller logs registration before logging the user into the session; this order preserves an audit trail even if later redirection fails.
- `Auth::login($user)` creates an authenticated session immediately after registration, reducing friction.
- Login uses `Auth::attempt($credentials, remember)` followed by session regeneration, which is the correct defense against session fixation.
- Failed login returns back with validation-style error messaging rather than throwing an exception, which is user-friendly and conventional.
- Logout writes an audit record before invalidating the session, so the acting user is still available for attribution.
- State effect: creates users, mutates session state, and appends audit rows.

#### `DashboardController.php`

Logical blocks:
- Authenticated user loading.
- Recent payments query.
- View assembly.

Analysis:
- The controller eagerly loads `role`, `addresses`, `subscriptions.plan`, and `subscriptions.address`, which preempts common N+1 patterns on the dashboard screen.
- Recent payments are filtered through `whereHas('subscription')` plus `whereBelongsTo($user)` semantics, which keeps user scoping in SQL rather than filtering in PHP.
- Limiting recent payments prevents unbounded rendering costs on the landing screen.
- State effect: read-only; no persistence.

#### `HomeController.php`

Logical blocks:
- Active plans query.
- Public view rendering.

Analysis:
- This controller intentionally depends only on active plan records, which makes the landing page data-driven instead of hard-coded.
- It is read-only and has no auth dependencies.
- State effect: none beyond view response generation.

#### `SubscriptionPlanController.php`

Logical blocks:
- Active plan listing.

Analysis:
- Fetches only `is_active = true` plans, preventing retired plans from being sold or shown.
- This controller is minimal by design because Team 1 has not implemented admin plan management screens.
- State effect: none.

#### `AddressController.php`

Logical blocks:
- List screen data loading.
- Address creation.
- Address update.
- Address deletion.
- Default-address normalization.

Analysis:
- `index()` loads addresses ordered for stable rendering and keeps the screen self-contained with both listing and forms.
- `store()` normalizes `country` to uppercase before persistence, which protects consistency across downstream shipping logic.
- On first address creation, default behavior is forced even if the user does not explicitly request it; this removes an ambiguous “no default address” state.
- When `is_default` is true, sibling addresses are reset to false before inserting/updating the target address, preserving the single-default invariant at the application layer.
- `update()` scopes lookup to `$request->user()->addresses()`, preventing horizontal privilege escalation by ID guessing.
- `destroy()` also scopes deletion to the current user and reassigns a fallback default if the deleted row was default and another address remains.
- Audit logging after create/update/delete provides a clean trace for mutable customer profile data.
- Risk note: default-address invariants are not backed by a database-level partial unique index, so concurrent requests could still race.
- State effect: inserts, updates, deletes addresses and appends audit entries.

#### `SubscriptionController.php`

Logical blocks:
- List screen data loading.
- Subscription creation guard.
- Pause action.
- Resume action.
- Plan change action.
- Ownership/admin gate helper.

Analysis:
- `index()` eagerly loads plans, addresses, and user subscriptions with related plan/address data so the page can render creation and management controls without lazy queries.
- Before creating a subscription, the controller blocks if the user already has an `active` or `paused` subscription, which encodes a business rule at the HTTP layer.
- The actual create/pause/resume/change-plan work is delegated to `SubscriptionService`, which keeps the controller thin and transaction logic centralized.
- `findAccessibleSubscriptionOrFail()` allows admins to act across users while ordinary users remain scoped to their own subscriptions.
- `changePlan()` validates the new plan through `UpdateSubscriptionPlanRequest`, then delegates billing and state mutation together.
- State effect: creates subscriptions/payments/audit rows through the service and mutates subscription state.

#### `PaymentController.php`

Logical blocks:
- User-scoped payment query.
- Pagination.

Analysis:
- Payments are not fetched by `user_id` directly because the schema ties them to subscriptions, not users.
- The controller therefore queries `Payment` with subscription and plan eager loads, then restricts rows by subscriptions belonging to the authenticated user.
- Pagination keeps the billing history view bounded and avoids loading an arbitrary lifetime of rows.
- State effect: read-only.

#### `AuditLogController.php`

Logical blocks:
- Admin-scoped audit query.
- Pagination.

Analysis:
- Logs are eager-loaded with their `user` relation because the view displays the actor email for each row.
- Pagination at 20 per page is the first necessary step toward audit-table scalability.
- Authorization is externalized to routing rather than embedded here.
- State effect: read-only.

#### `BoxController.php`

Logical blocks:
- Box list query.
- Single box query.
- Owner/admin access checks.

Analysis:
- `index()` loads boxes through the current user’s subscriptions unless the user is admin, which keeps the surface aligned with business ownership.
- `show()` loads nested relationships needed for the detail screen and verifies accessibility before rendering.
- The helper `ensureAccessible()` duplicates some ownership semantics that also exist in `Box::ownedBy()`, which is simple but slightly redundant.
- State effect: read-only.

#### `BoxCustomizationController.php`

Logical blocks:
- Customization screen assembly.
- Swap request delegation.
- Remove request delegation.
- Access checks.

Analysis:
- The customization screen loads the box plus related items and available replacement items; this is necessary because swaps need both current and candidate inventory.
- Swap requests validate through `SwapItemRequest`, then hand off all real business checks to `BoxCustomizationService`.
- Remove requests operate on a concrete `BoxItem` row and verify it belongs to the target box before mutation.
- Access checks remain duplicated at controller level for defense in depth even though services also enforce business rules like lock windows.
- State effect: updates `box_items`, box weight totals, shipping tier, and potentially flash warning session state.

### 3.3 Middleware

#### `RoleMiddleware.php`

Logical blocks:
- User extraction.
- Role-name comparison.
- Failure abort.

Analysis:
- The middleware assumes `auth` already ran and therefore works against `$request->user()`.
- It compares the user role name against the variadic middleware parameter list, which makes the route declaration expressive and compact.
- Failure path is `abort(403)`, which is appropriate for authenticated-but-forbidden requests.
- Risk note: role checks depend on relation availability; if the role relation is missing or null because of corrupted data, the request is denied by default, which is safe.
- State effect: none; it only short-circuits request flow.

### 3.4 Form Requests

#### `LoginRequest.php`

Analysis:
- Validates only email and password, keeping login strict and small.
- Authorization method returns true, so route access control remains the concern of middleware.
- State effect: none; it filters input.

#### `RegisterUserRequest.php`

Analysis:
- Enforces unique email and confirmed password with minimum length.
- Includes `name` and `phone`, reflecting the customized user schema rather than Laravel default scaffolding.
- The rule set is good for a student project but does not yet enforce phone format rigor or password uncompromised checks.

#### `AddressRequest.php`

Analysis:
- Enforces required street, city, and country fields with optional region/postal code/default flag.
- Country size-two validation aligns with ISO-like country codes expected by the schema.
- This request keeps address normalization concerns mostly in the controller, not the validator.

#### `StoreSubscriptionRequest.php`

Analysis:
- Validates existence of selected plan and address rows plus starting date and flags.
- It trusts any address ID that exists, not specifically an address owned by the current user.
- The controller/service therefore rely on higher-level logic or database semantics rather than ownership-sensitive validation, which is a correctness gap.

#### `UpdateSubscriptionPlanRequest.php`

Analysis:
- Validates only that the requested plan exists.
- Does not block choosing the same plan, so no-op changes are possible unless the service rejects them indirectly.

#### `SwapItemRequest.php`

Analysis:
- Validates target pivot row ID, target item ID, and optional confirmation booleans.
- This request intentionally does not encode rotation/allergen business rules because those require contextual database checks.

### 3.5 Models

#### `User.php`

Logical blocks:
- Trait mixins.
- Mass assignment/hidden config.
- Attribute casting.
- Relations.
- Admin role helper.

Analysis:
- `HasUuids` moves the project to opaque user IDs and stays consistent with the migration.
- Attribute-based `Fillable`/`Hidden` is modern Laravel style and removes older property boilerplate.
- The password cast keeps hashing/typing conventions centralized in the model layer.
- Relations are straightforward and form the root of most ownership queries in the codebase.
- `isAdmin()` is a convenience method that collapses a common role-name comparison into one semantic predicate.
- Risk note: role comparison is string-based, so role renames are dangerous unless constants remain authoritative.

#### `Role.php`

Analysis:
- Defines `ADMIN` and `SUBSCRIBER` constants, which avoids magic strings scattered across controllers and seeders.
- Has a single `users()` relation because role behavior is deliberately simple.
- State effect: none; domain metadata only.

#### `Address.php`

Analysis:
- UUID model with fillable delivery fields and boolean cast for default state.
- `belongsTo(User::class)` and `hasMany(Subscription::class)` encode its reuse across subscriptions.
- The model is intentionally thin because lifecycle rules live in the controller.

#### `SubscriptionPlan.php`

Analysis:
- Casts `price_monthly`, `features`, and `is_active`, which ensures views and services work with usable PHP values rather than raw strings.
- Maintains only a `subscriptions()` relation because plans are reference data, not behavior-heavy entities.

#### `Subscription.php`

Analysis:
- UUID model with date, integer, and boolean casts matching billing semantics.
- Exposes `user`, `plan`, `address`, `payments`, and `boxes` relations, making it the central aggregate root of Team 1.
- The model intentionally does not contain state-transition methods; those sit in `SubscriptionService`.

#### `Payment.php`

Analysis:
- UUID model with decimal casts for `amount` and `tax_amount`.
- Single `subscription()` relation reflects normalized billing design.
- Because the schema stores gateway metadata, the model can evolve into a real payment abstraction later.

#### `AuditLog.php`

Analysis:
- Disables default timestamps because the table uses a single `created_at` field rather than both `created_at` and `updated_at`.
- Casts `metadata` to array for ergonomic JSON handling in views and services.
- Optional `user()` relation reflects system-generated entries.

#### `Box.php`

Analysis:
- UUID model with date/weight/status casts and relations to subscription, items, and customization.
- `ownedBy(User $user)` localizes the ownership predicate and prevents repeated subscription-to-user traversal logic.
- Risk note: ownership semantics also exist in controllers; that duplication can drift.

#### `BoxCustomisation.php`

Analysis:
- Thin UUID model with one-to-one `box()` relation.
- Its behavior is almost entirely service-driven.

#### `BoxItem.php`

Analysis:
- Functions as an explicit model for pivot rows because extra fields like quantity and flags exist.
- Lacks explicit `box()` and `item()` relations, which weakens discoverability and encourages raw ID usage in controllers/services.

#### `Item.php`

Analysis:
- UUID model with mass-assignable catalog fields and relevant casts.
- `belongsToMany(Box::class, 'box_items')` enables box composition through pivot records.
- Missing dedicated relations for allergens is notable because allergen logic in services falls back to raw queries.

### 3.6 Services

#### `AuditLogService.php`

Logical blocks:
- `log()` write helper.

Analysis:
- Accepts nullable user, nullable entity fields, metadata array, and IP address, making it broad enough for both user and system actions.
- Centralizing audit persistence avoids repeated `AuditLog::create()` payload assembly across controllers.
- Synchronous execution is acceptable at current scale because log writes are small and local.

#### `TaxService.php`

Logical blocks:
- `calculateTax()` arithmetic helper.

Analysis:
- Reads `subscriptions.tax_rate` from config, which is the correct boundary between business rules and code.
- Uses simple rounding to two decimals, which is fine for demonstration but ignores jurisdiction-specific tax rules and decimal precision policy.

#### `BillingService.php`

Logical blocks:
- Plan retrieval.
- Tax calculation.
- Payment creation.

Analysis:
- The service creates a payment row for a given subscription and reason, treating billing as pure internal bookkeeping.
- It always marks status as success, which intentionally simulates a happy-path gateway and keeps the project teachable.
- Gateway reference and reason code are stored, giving later extensibility without requiring a real PSP integration now.
- State effect: inserts `payments`.

#### `SubscriptionService.php`

Logical blocks:
- Subscription creation.
- Pause.
- Resume.
- Plan change.
- Renewal sweep.

Analysis:
- `createForUser()` wraps subscription creation, initial payment generation, and audit logging in one transaction, which is the correct aggregate boundary.
- The subscription row stores billing-derived state such as `next_billing_date`, `remaining_billing_days`, `auto_renew`, and `loyalty_points`, making recurring logic queryable.
- `pause()` records remaining billing days before nulling next billing date, which preserves recoverable billing state.
- `resume()` restores status and computes a future billing date from saved remaining days.
- `changePlan()` updates the plan and immediately creates a payment row describing the change reason, so billing history mirrors lifecycle events.
- `renewDueSubscriptions()` eagerly loads `plan` and `user`, scans due active subscriptions, and renews each in a transaction while incrementing loyalty points.
- Risk note: the renewal sweep loops row-by-row and can become expensive on large datasets.
- Risk note: there is no `withoutOverlapping()` scheduler guard or DB-level lock, so concurrent scheduler instances could double-charge.

#### `WeightService.php`

Logical blocks:
- Weight total recalculation.
- Shipping tier determination.
- Limit enforcement.

Analysis:
- `recalculate()` sums item weights from the already-loaded or lazy-loaded relation and updates both `total_weight_g` and `shipping_tier`.
- Tier computation depends on `config/shipping.php`, which is the correct externalization of threshold data.
- Limit enforcement provides a reusable predicate for box customization rules.
- Risk note: if `items` is not eager loaded, summing can trigger extra queries.

#### `ThemeRotationService.php`

Logical blocks:
- Previous-period lookup.
- Duplicate-item existence check.

Analysis:
- The service asks whether a candidate item appeared in the previous box period for the same subscription.
- It uses explicit table queries rather than relations, which is acceptable but less expressive and harder to refactor safely.
- This is a policy-like service even though it is named around theme rotation rather than duplication rules.

#### `BoxCustomizationService.php`

Logical blocks:
- Remove item flow.
- Swap item flow.
- Lock-state guard.
- Weight check.
- Rotation check.
- Allergen conflict check.
- Recalculation.

Analysis:
- The service is the richest business-rule object in the codebase.
- It rejects edits when a box is locked by status or date, preserving the immutable shipping boundary.
- Swap flow supports “warning then confirm” semantics for repeated items or allergen conflicts by flashing session warnings instead of hard-failing outright.
- The service recalculates box weight after each mutation so shipping tier stays synchronized with contents.
- It uses raw queries for allergen checks because model relations are incomplete for that subdomain.
- Risk note: service methods likely mix domain decisions, persistence, and session-flash UX concerns, which slightly violates SRP.

### 3.7 Providers

#### `AppServiceProvider.php`

Analysis:
- `register()` and `boot()` are empty.
- This is not a defect by itself; it simply means no custom container bindings or app boot hooks have been required yet.

### 3.8 Configuration Files

#### `config/auth.php`

Analysis:
- Uses Laravel’s standard session guard and Eloquent provider.
- This aligns with `AuthController` and keeps auth infrastructure conventional.

#### `config/session.php`

Analysis:
- The app uses database-backed sessions, so `sessions` support must exist before the first page load.
- This design improves inspectability but raises migration correctness to a runtime-critical concern.

#### `config/cache.php`

Analysis:
- Database cache driver means features like `cache:clear` and any runtime cache writes hit PostgreSQL.
- This is why partial migrations previously caused immediate runtime failures.

#### `config/queue.php`

Analysis:
- Database queue connection means the queue listener cannot operate until jobs tables exist.
- The local `composer run dev` workflow therefore depends directly on successful migrations.

#### `config/subscriptions.php`

Analysis:
- Encodes the tax rate and billing cycle length.
- This is the correct place for simple tunable business constants.

#### `config/shipping.php`

Analysis:
- Encodes weight ceilings and tier breakpoints for box logic.
- Keeps numbers out of services and makes box shipping logic inspectable.

### 3.9 Migrations

#### `0001_01_01_000000_create_users_table.php`

Logical blocks:
- Users table.
- Password reset or related auth support if present.
- Sessions table.

Analysis:
- Replaces Laravel’s default big integer user IDs with UUIDs, which forces downstream foreign keys to match.
- Adds `phone`, reflecting a domain-specific extension of the user profile.
- Creates a sessions table compatible with the configured database session driver.
- State effect: foundational auth schema.

#### `0001_01_01_000001_create_cache_table.php`

Analysis:
- Creates `cache` and `cache_locks`.
- Because the app uses database caching, this migration is operationally mandatory, not optional infrastructure.

#### `0001_01_01_000002_create_jobs_table.php`

Analysis:
- Creates queue-related tables.
- Required for `queue:listen` in local development because the configured queue connection is database-backed.

#### `2026_04_26_035700_create_subscription_platform_schema.php`

Logical blocks:
- `pgcrypto` extension enablement.
- Core reference tables.
- Customer profile tables.
- Subscription and billing tables.
- Box, logistics, claims, reward, promo, gift, social, notification, and retention tables.
- Trigger function and `updated_at` triggers.
- Index creation.
- Seed inserts for roles and plans.
- `users.role_id` schema extension.

Analysis:
- This is the dominant schema artifact and it is implemented as raw PostgreSQL SQL inside a Laravel migration.
- The positive side is exact control over UUID defaults, triggers, partial domain constraints, and PostgreSQL-specific features like `JSONB`.
- The negative side is reduced readability inside a Laravel codebase, weaker framework-level portability, and harder incremental refactoring.
- The migration mixes DDL, indexes, trigger setup, and seed data in one giant concern, which violates the “one concern per migration” principle.
- It creates far more tables than Team 1 currently uses directly, meaning the schema is ahead of the implemented application surface.
- It seeds roles and subscription plans directly in the migration, then the seeder also maintains them, which duplicates responsibility.

#### `2026_04_26_035736_create_boxes_table.php`

Analysis:
- Guards with `Schema::hasTable('boxes')` to avoid duplicate creation if the large SQL migration already ran.
- This is a pragmatic repair migration for inconsistent environments rather than a clean first-principles schema artifact.

#### `2026_04_26_040242_create_box_customisations_table.php`

Analysis:
- Mirrors the fallback pattern used for boxes.
- Again pragmatic, but it signals earlier migration-order instability.

### 3.10 Seeder and Factory

#### `DatabaseSeeder.php`

Logical blocks:
- Role upserts.
- Plan upserts.
- Demo user creation.
- Demo admin creation.

Analysis:
- Uses idempotent operations rather than blind inserts, which is correct for rerunnable local setup.
- Creates one subscriber and one admin with predictable emails for demonstration and review.
- It depends on roles already existing or being upserted in the same method, which is satisfied.
- Risk note: embedding known demo credentials is acceptable for local education only.

#### `UserFactory.php`

Analysis:
- Ensures a subscriber role exists before creating users, which makes isolated tests less brittle.
- Generates coherent user data and hashed passwords.
- This factory is aligned with the customized user schema rather than Laravel defaults.

### 3.11 Views

#### `layouts/app.blade.php`

Logical blocks:
- HTML shell and asset loading.
- Top navigation.
- Flash message rendering.
- Content slot.

Analysis:
- `@vite` loads app assets once for the shared shell.
- Navigation changes based on auth state, exposing only relevant actions.
- Flash surfaces for success/error messages standardize mutation feedback across the app.
- State effect: none; presentation only.

#### `welcome.blade.php`

Analysis:
- Public marketing screen that sells the product concept while reading active plans from the database.
- Presentation is intentionally more branded than the admin-style internal screens.
- It keeps public onboarding soft while the authenticated app remains utilitarian.

#### `auth/login.blade.php`

Analysis:
- Simple form posting to named login route with CSRF protection.
- Demo credentials are shown, which is helpful for reviewers but unsafe for production.
- Validation errors and session feedback depend on the shared layout or form-level surfaces.

#### `auth/register.blade.php`

Analysis:
- Mirrors the login page with fields for name, phone, email, password, and password confirmation.
- Relies on server-side validation rather than client-side complexity.

#### `dashboard.blade.php`

Analysis:
- Aggregates user role, address count, current subscription summary, quick actions, and recent payments.
- This screen is the clearest expression of Team 1 scope because it links auth, subscriptions, addresses, billing, and admin audit access.

#### `addresses/index.blade.php`

Analysis:
- Combines create form and update/delete controls on one screen, reducing route sprawl.
- Uses named routes and method spoofing correctly.
- Default-address UX is explicit instead of hidden.

#### `plans/index.blade.php`

Analysis:
- Public read-only plan grid.
- Reflects plan features and price cleanly with no write actions.

#### `subscriptions/index.blade.php`

Analysis:
- Handles both creation and lifecycle management on a single screen.
- Guards creation when no address exists, which enforces a real dependency without extra controller complexity.
- Offers pause/resume/change-plan controls using named routes and CSRF-safe forms.

#### `payments/index.blade.php`

Analysis:
- Renders a paginated table with plan, reason, tax, amount, status, and date.
- Uses nullsafe plan access and formatted decimals, matching the casting decisions in the model.

#### `audit-logs/index.blade.php`

Analysis:
- Keeps design intentionally plain so the reviewer reads the log content instead of navigating a heavy UI.
- Pretty-prints JSON metadata inline, which is excellent for debugging but could expose too much detail if logs ever contain secrets.

#### `boxes/index.blade.php`

Analysis:
- Lists boxes with summary fields and a route into detailed review.
- Keeps the box domain discoverable without exposing write actions immediately.

#### `boxes/show.blade.php`

Analysis:
- Shows a single box, status/lock context, and current items.
- Functions as the safe read-only checkpoint before customization.

#### `boxes/customize.blade.php`

Logical blocks:
- Alpine modal state bootstrap.
- Flash alerts.
- Lock banner.
- Weight/tier progress.
- Current items grid.
- Swap modal with confirm-warning branch.

Analysis:
- This view is materially more interactive than the rest of the codebase and embeds Alpine state directly in the template.
- It computes lock state and weight percentage inside Blade/PHP blocks, which is acceptable for lightweight presentation but shifts some view model logic into the template.
- Swap warnings use session-driven modal reopening so the user can confirm risky changes without losing context.
- External Alpine CDN inclusion is convenient but operationally weaker than bundling through Vite.

## Phase 4: Security, Correctness, and Architecture Audit

### 4.1 Security

Strengths:
- Session fixation is mitigated by regenerating the session on login.
- All mutating forms include CSRF protection.
- Eloquent and query builder usage prevent classic SQL injection in the implemented flows.
- Route-level `auth` and `role:admin` guards correctly separate public, authenticated, and admin surfaces.
- User-owned resource access is usually enforced by scoping queries through `$request->user()->relation()`.
- Blade output uses escaped `{{ }}` rendering by default, which blocks stored XSS unless raw HTML is intentionally introduced later.

Weaknesses:
- `StoreSubscriptionRequest` validates only that `address_id` exists, not that it belongs to the authenticated user.
- Authorization is mostly controller-scoped rather than policy-scoped, so access logic is duplicated and easier to miss on future routes.
- Audit metadata pretty-printing is safe from XSS because of Blade escaping, but it may still surface sensitive internal data if future metadata grows.
- Demo credentials in `DatabaseSeeder` and `login` view are acceptable only in local/dev contexts.

### 4.2 Data Integrity

Strengths:
- UUIDs are consistent across major business entities.
- Foreign keys and check constraints in the large SQL migration are much richer than a typical student schema.
- Subscription lifecycle mutations use transactions in the service layer.
- Seeder logic is idempotent.

Weaknesses:
- The giant raw SQL migration mixes schema creation and seed data, which complicates rollback and incremental evolution.
- Default-address uniqueness is enforced in code, not at the database level.
- Fallback migrations for boxes/customisations indicate earlier migration-order defects and environment drift.
- Team 1 implemented logic relies on a much larger schema than the currently exposed UI actually needs.

### 4.3 Performance

Strengths:
- Dashboard, payments, and audit logs use eager loading and pagination where it matters.
- Subscription renewal preloads related user and plan data.
- Public plan pages query only active plans.

Weaknesses:
- Renewal sweep is linear over all due subscriptions and processes each in its own transaction; this is acceptable now but will degrade with volume.
- Box customization services use several raw existence queries and can accumulate query count under repeated swaps.
- Weight recalculation may trigger additional relation loads if items were not preloaded.
- No lazy-loading prevention is configured, so accidental N+1 regressions can slip in silently during development.

### 4.4 Design Quality

Strengths:
- Team 1 domain logic is mostly layered correctly: controllers delegate to services, models remain relatively thin, validation is externalized to form requests.
- Configuration values such as tax rate, billing cycle, and shipping thresholds are not hard-coded into controllers.
- Audit logging is centralized rather than copy-pasted.

Weaknesses:
- Box customization service blends domain rules, persistence, and UX warning/session concerns.
- Authorization logic is spread across middleware, controllers, helper methods, and model helpers rather than formalized as policies.
- The codebase combines foundational subscription logic and downstream box logic in one deployment slice, increasing cognitive load.
- The large schema migration is architecturally ahead of the implemented application behavior and therefore harder to reason about.

## Phase 5: Focused Improvement List

| Severity | File/Area | Issue | Why It Matters | Recommended Fix |
| --- | --- | --- | --- | --- |
| Critical | `StoreSubscriptionRequest.php` / subscription creation flow | `address_id` ownership is not validated against the current user. | A user can potentially attach another user’s address if they know its UUID. | Replace plain `exists:addresses,id` with ownership-aware validation or recheck in controller/service before creation. |
| High | `database/migrations/2026_04_26_035700_create_subscription_platform_schema.php` | One giant raw SQL migration mixes DDL, triggers, indexes, and seed data. | It is hard to evolve, review, and recover when one section fails. | Split into concern-specific Laravel migrations and move seed data fully into seeders. |
| High | Address default logic | Single-default invariant exists only in application code. | Concurrent requests can produce multiple defaults. | Add a PostgreSQL partial unique index for `(user_id) WHERE is_default = true` plus transaction-safe update logic. |
| High | Renewal scheduling in `routes/console.php` / `SubscriptionService.php` | No overlap guard or distributed lock protects the renewal sweep. | Multiple scheduler instances can double-charge or double-renew subscriptions. | Move to a command class and schedule with `withoutOverlapping()` and, if needed, `onOneServer()`. |
| Medium | Authorization architecture | Ownership checks are duplicated across controllers and model helpers. | Duplication invites drift and future exposure bugs. | Introduce Laravel policies for subscriptions, addresses, boxes, and audit access. |
| Medium | `BoxItem.php` / item-allergen domain | Relations are incomplete, causing raw queries in services. | Query logic becomes harder to read and maintain. | Add explicit relations on `BoxItem`, `Item`, and allergen-related models, then replace raw queries with expressive Eloquent or focused query objects. |
| Medium | `BoxCustomizationService.php` | Session warning UX is mixed into core domain service methods. | Service boundaries blur and testing becomes harder. | Return structured domain outcomes from the service and let the controller translate them into flash/session responses. |
| Medium | `UpdateSubscriptionPlanRequest.php` / plan change flow | No validation prevents changing to the same plan. | Creates no-op billing or confusing history if the service allows it. | Reject identical plan changes before billing logic runs. |
| Low | `AppServiceProvider.php` / development safety | No lazy-loading prevention or model strictness is enabled in development. | N+1 and attribute typos can slip in quietly. | Enable strict model behavior in non-production environments. |
| Low | `resources/views/boxes/customize.blade.php` | Alpine is loaded from an external CDN in-template. | Adds an external runtime dependency and bypasses normal asset bundling. | Bundle Alpine through Vite or move modal behavior into local JS assets. |

## Phase 6: Learning Synthesis

### 6.1 Ten Most Important Laravel Concepts Demonstrated Here

1. Session-based authentication with explicit login, logout, and session regeneration.
2. Form Request validation as the boundary between raw HTTP input and application logic.
3. Eloquent relationships as the primary data-navigation API.
4. Service-layer orchestration for multi-step business workflows.
5. Route middleware grouping for access-control structure.
6. Database transactions for subscription lifecycle atomicity.
7. Config-driven business constants for tax, billing cycle, and shipping rules.
8. Database-backed session/cache/queue drivers and their operational implications.
9. Blade as a server-rendered UI layer with named routes and CSRF-safe forms.
10. Scheduled background work through `routes/console.php` and the scheduler.

### 6.2 Ten Most Important Weaknesses in the Current Implementation

1. Subscription address ownership is not enforced strongly enough at validation time.
2. The main schema migration is too large and mixes multiple concerns.
3. Database invariants such as “one default address per user” are not enforced in SQL.
4. Renewal scheduling lacks overlap protection.
5. Authorization is duplicated instead of policy-driven.
6. Box customization service mixes domain logic and UI warning flow.
7. Raw SQL/query fragments appear where relations should exist.
8. The schema surface is larger than the implemented feature surface, increasing maintenance burden.
9. Development-time strictness against lazy loading and attribute mistakes is absent.
10. Demo/dev credentials are visible in seeded/local-facing flows and must never survive environment promotion.

### 6.3 Minimum Refactor Path to Reach a Stronger Foundation

1. Formalize policies for addresses, subscriptions, boxes, and audit logs.
2. Fix ownership validation for `address_id` and other foreign-key inputs derived from user-owned resources.
3. Split the giant PostgreSQL migration into smaller Laravel-native migrations and move all seed data to seeders.
4. Add database-level invariants for single-default address and any other cardinality assumptions now enforced only in PHP.
5. Promote the renewal closure into a dedicated command class with scheduler overlap protection.
6. Refactor box customization outcomes into domain result objects or exceptions, leaving session flash behavior to controllers.
7. Complete missing model relations for allergens and pivot entities to reduce raw query usage.
8. Enable strict Eloquent behavior in non-production environments to catch N+1 and missing-attribute bugs early.
9. Add focused feature tests for authorization boundaries and lifecycle edge cases, especially plan changes, pause/resume, and box swap warnings.
10. Separate Team 1 foundational modules from downstream box/logistics modules conceptually, even if they stay in one repository.
