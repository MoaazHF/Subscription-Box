# Mandatory Design Patterns Applied

## 1) Strategy Pattern
- Context: Notification dispatch.
- Implementation: `ChannelDispatcher` delegates to concrete channel strategies (`LogNotificationChannel`, `DatabaseNotificationChannel`) selected by notification type.
- Benefit: channel behavior is extensible without changing caller logic.

## 2) Facade/Service Layer Pattern
- Context: domain operations orchestration.
- Implementation: `SubscriptionService`, `BoxCustomizationService`, `BundleSelectorService`, `DeliveryStateTransitionService`, `ClaimResolutionService`.
- Benefit: controller code stays thin, logic is reusable and testable.

## 3) Template Method via Framework Base Classes
- Context: request validation and authorization workflow.
- Implementation: FormRequest subclasses (`StoreClaimRequest`, `ResolveClaimRequest`, `ReportFilterRequest`, etc.) implement framework hooks (`authorize`, `rules`).
- Benefit: consistent validation pipeline and reduced duplication.
