# GTM Lens Agent Instructions

This is the first file agents should read before changing this repository.

## Project Status

Stage 0 is complete and published. Stage 1A is the current implementation branch for a Salesforce CLI metadata probe.

- Repository: `https://github.com/bpero-mural/gtmlens`
- Current branch: `stage-1a-cli-metadata-probe`
- Current stage: Stage 1A Salesforce CLI metadata probe
- OAuth is still intentionally out of scope.

## Stage 0 Baseline

The app is a Docker-first Laravel modular monolith foundation:

- Laravel 13
- PHP 8.4
- PostgreSQL 17
- Blade
- Livewire 4
- Tailwind CSS
- Local email/password auth
- Database queues
- Laravel scheduler
- Stage 0 core schema
- Tests for the Stage 0 contract

## Local Login

Stage 0 auth is intentionally simple.

- URL: `http://localhost/login`
- Email: `admin@example.test`
- Password: `password`

Do not add social login, Microsoft Entra ID, SSO, OAuth UI, or identity-provider packages until the user explicitly starts that work.

The local admin is repaired by `php artisan gtm:ensure-local-admin` and by the local login flow if the user is missing or has a stale hash.

## Stage 1A Boundary

Stage 1A may use Salesforce CLI to read allowlisted metadata from an already-authenticated org alias:

```bash
php artisan salesforce:metadata-probe {orgAlias}
```

Do not implement these unless the user explicitly asks for the next stage:

- Salesforce OAuth
- Salesforce token storage
- Search implementation
- Dependency parsers
- Data dictionary editing
- Timeline diffing
- Potential issue detectors

## Security Boundary

The MVP must not query or store Salesforce business record data.

Never send Salesforce metadata, credentials, source code, screenshots, internal docs, or product details to external SaaS unless the user explicitly approves that transfer and the security docs are updated.

Stage 1A metadata queries must pass the allowlist guard. `Account`, `Contact`, `Opportunity`, `SELECT *`, and unknown objects are forbidden.

## Design Boundary

Use `DESIGN.md` as the local design-system contract for UI work.

The project may use Google's open DESIGN.md format as a structure, but do not copy Google visual identity, Google Stitch output, Google branding, or third-party brand systems.

Do not use Google Stitch or another external design SaaS with project data in the MVP.

## Google DESIGN.md Legal And Vendor Boundary

Google's public materials describe DESIGN.md as an open draft specification intended to work across tools and platforms. The public GitHub repository identifies the project as Apache-2.0 licensed.

For GTM Lens:

- Allowed: hand-authored local `DESIGN.md` files that describe GTM Lens-owned colors, typography, spacing, and UI rules.
- Allowed with review: adding Google's `@google/design.md` CLI only if its license is documented, it is locked in `package-lock.json`, and it runs locally without sending data outside the machine.
- Not allowed in the MVP: uploading screenshots, Salesforce metadata, source code, credentials, or internal product details to Google Stitch or any external design SaaS.
- Not allowed: claiming GTM Lens is endorsed by, affiliated with, or built by Google.
- Not allowed: using Google logos, Google product names as branding, Material/Google trade dress, or Stitch-generated output without explicit approval.

This is an engineering compliance note, not legal advice. If external distribution, marketing use, or procurement of Google design tooling is planned, get legal/procurement review first.

## Brand Boundary

GTM Lens is an internal application name. Do not introduce corporate logos, proprietary brand assets, or external-facing claims unless the user provides approved assets and usage rules.

## UI Rules

- Keep the frontend Laravel-native: Blade, Livewire, Tailwind CSS, and custom Blade components.
- Keep the app operational, compact, table-first, and metadata-oriented.
- Do not add Vue, Inertia, Nuxt UI, PrimeVue, Filament, Flux, Mary UI, DaisyUI, AG Grid, Cytoscape.js, graph visualization libraries, paid UI kits, or marketplace blocks.
- Do not use oversized hero pages inside the authenticated app.
- Do not use decorative gradient backgrounds, orbs, bokeh, or brand-copycat styling.

## Verification

Use Docker-first commands:

```bash
docker compose up --build -d
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
docker compose exec app php artisan test
docker compose exec app npm run build
```

Run `docker compose exec app php artisan salesforce:metadata-probe stage` only when an authenticated Salesforce CLI alias named `stage` is intentionally available.

Before committing, verify that `.env`, `vendor`, `node_modules`, `public/build`, and caches are not staged.

## Docs To Keep In Sync

- `README.md`
- `ROADMAP.md`
- `DESIGN.md`
- `docs/security.md`
- `docs/license-policy.md`
- `docs/docker.md`
