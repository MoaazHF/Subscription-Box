# Phase 2 Traceability Matrix

## Cover-Sheet Items Mapping

| Item | Requirement | Evidence | Status |
|---|---|---|---|
| 1 | Functional Requirements | `docs/phase2/functional-requirements.md` | Complete |
| 2 | Non-Functional Requirements | `docs/phase2/non-functional-requirements.md` | Complete |
| 3 | Use-Case Diagram(s) | `docs/phase2/diagrams/use-case.drawio` | Complete |
| 4 | System Architecture | `docs/phase2/diagrams/system-architecture.drawio` | Complete |
| 5 | Activity Diagrams | `docs/phase2/diagrams/activity.drawio` | Complete |
| 6 | Object Diagrams | `docs/phase2/diagrams/object.drawio` | Complete |
| 7 | Package Diagram(s) | `docs/phase2/diagrams/package.drawio` | Complete |
| 8 | Sequence/SSD | `docs/phase2/diagrams/sequence-ssd.drawio` | Complete |
| 9 | DB Specification (ERD, Tables) | `docs/phase2/diagrams/erd.drawio`, `database/migrations/0001_01_01_000000_create_users_table.php` | Complete |
| 10 | Collaboration/Communication | `docs/phase2/diagrams/collaboration.drawio` | Complete |
| 11 | Class Diagram v2/v3 | `docs/phase2/diagrams/class-v2.drawio`, `docs/phase2/diagrams/class-v3.drawio` | Complete |
| 12 | Three Design Patterns + Description | `docs/phase2/design-patterns.md` | Complete |
| 13 | Front-End Design for Functions | Blade views in `resources/views/*` + Tailwind utility system | Complete |
| 14 | Implementation aligned with requirements and modules | Routes/controllers/services/tests | Complete |
| 15 | Complexity & Testing | `tests/Feature/*` and new Phase 2 coverage tests | Complete |

## SRS Function Mapping

| Function | Description | Evidence | Status |
|---|---|---|---|
| F1 | Multi-tier plans | `SubscriptionPlanController`, `plans.index` | Implemented |
| F3 | Pause/Resume | `SubscriptionService::pause/resume` | Implemented |
| F4 | Auto-renewal | `subscriptions:renew` + scheduler | Implemented |
| F5 | Upgrade/Downgrade | `SubscriptionService::changePlan` | Implemented |
| F10 | Billing sync | `BillingService` + `payments` | Implemented |
| F11 | Tax calculation | `TaxService` | Implemented |
| F13 | Swap checker | `BoxCustomizationService::swap` | Implemented |
| F14 | Personalized surprise | `box_items.is_surprise` + provisioning logic | Partial |
| F15 | Lock-in date | `boxes.lock_date` + guard checks | Implemented |
| F16 | Add-ons | `BoxCustomizationService::add` | Implemented |
| F17 | Limited stock | `StockAllocationService` atomic reserve/release | Implemented |
| F18 | Allergy filter | allergen conflict checks in customization service | Implemented |
| F19 | Bundle selector | `Bundle` module + apply bundle to box flow | Implemented |
| F20 | Weight calculator | `WeightService` | Implemented |
| F21 | Source info | item supplier/origin/sourcing fields and admin CRUD | Implemented |
| F22 | Theme rotation | `ThemeRotationService` confirmation path | Implemented |
| F23 | Duplicate prevention | duplication guards in add/swap flows | Implemented |
| F24 | Delivery status machine | `DeliveryStateTransitionService` | Implemented |
| F25 | Notifications | channel dispatcher + queued jobs + retry metadata | Implemented |
| F26 | Delivery instructions | delivery fields/controllers/views | Implemented |
| F27 | Damage claim | claim submission + admin resolution | Implemented |
| F28 | Missing box | overdue checker command + missing claims | Implemented |
| F29 | Eco shipping | subscription and delivery eco flags | Implemented |
| F30 | Address validation | `AddressRequest` + delivery linkage checks | Implemented |
| F40 | RBAC | role middleware + policy registration | Implemented |
| F41 | Audit log | `AuditLogService` and admin panel | Implemented |
