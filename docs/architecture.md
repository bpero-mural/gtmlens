# Architecture

GTM Lens is a Laravel modular monolith. Stage 0 establishes the application foundation without Salesforce OAuth or sync logic.

## Current Shape

- Laravel 13 on PHP 8.4.
- Blade, Livewire 4, Tailwind CSS, and custom Blade UI components.
- PostgreSQL 17 with `pg_trgm` and `unaccent` enabled by migration.
- Database-backed sessions, cache, and queues.
- Local filesystem storage for future metadata snapshots.

## Future Module Boundaries

- `app/Domain/Salesforce`
- `app/Domain/Metadata`
- `app/Domain/Search`
- `app/Domain/Dictionary`
- `app/Domain/Timeline`
- `app/Domain/Issues`
- `app/Domain/Audit`

These directories are intentionally not filled in Stage 0 because Salesforce connection and sync work starts in Stage 1.
