# Mural Lens

Mural Lens is an internal Laravel application for Salesforce metadata discovery, documentation, and impact analysis. Stage 0 creates only the local Docker foundation: auth, layout, migrations, queues, scheduler commands, docs, and tests.

## Requirements

- Docker Engine or a Podman-compatible Docker Compose setup.
- Optional host Node.js through `nvm` for convenience. Docker remains the source of truth.

## Local Setup

```bash
docker compose up --build
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
docker compose exec app php artisan test
docker compose exec app npm run build
```

Open `http://localhost` and sign in with:

- Email: `admin@example.test`
- Password: `password`

The app container resets this local development admin on boot. If the local database was edited manually, repair it with:

```bash
docker compose exec app php artisan mural:ensure-local-admin
```

## Common Commands

```bash
make up
make down
make migrate
make seed
make test
make queue
make schedule
make npm-dev
make npm-build
```

Equivalent direct commands:

```bash
docker compose exec app php artisan queue:work
docker compose exec app php artisan schedule:work
docker compose exec app npm run dev
```

## Optional Host Node

```bash
nvm use
npm install
npm run dev
npm run build
```

The app uses Node.js 22 LTS and enforces `"node": ">=22 <23"` in `package.json`.

## Stage Boundary

Stage 0 does not implement Salesforce OAuth, metadata sync, collectors, search behavior, or dependency parsing. Stage 1A adds a local Salesforce CLI metadata probe only; OAuth remains intentionally unimplemented.

## Stage 1A CLI Metadata Probe

Authenticate a Salesforce org outside Mural Lens with Salesforce CLI:

```bash
sf org login web --alias stage
```

Run the local metadata probe:

```bash
docker compose exec app php artisan salesforce:metadata-probe stage
```

The probe creates a `sync_run`, stores local JSON snapshots under `storage/app/snapshots`, and shows the org/run in `/salesforce-orgs` and `/sync-runs`.

Allowed metadata objects include `EntityDefinition`, `FieldDefinition`, `ApexClass`, `ApexTrigger`, `ValidationRule`, `Flow`, `FlowDefinition`, `FlowVersionView`, `CustomObject`, `CustomField`, and `PicklistValueInfo`.

Business-record queries remain forbidden. Do not query objects such as `Account`, `Contact`, or `Opportunity`.

## Important Files

- `docker-compose.yml` - local app, web, and PostgreSQL services.
- `docker/php/Dockerfile` - PHP 8.4 app container with Composer and Node 22.
- `docker/nginx/default.conf` - nginx web container config.
- `database/migrations` - auth, queue/cache/session, and core metadata schema.
- `resources/views` - Blade layout, UI components, dashboard, and placeholders.
- `docs` - architecture, security, license, Docker, Salesforce sync, Azure future, and ADRs.
