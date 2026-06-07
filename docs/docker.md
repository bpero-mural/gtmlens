# Docker

Docker Compose is the source of truth for local execution.

## Services

- `app`: PHP 8.4 FPM with Composer and Node.js 22.
- `web`: nginx 1.29 serving Laravel through PHP-FPM.
- `postgres`: PostgreSQL 17 with a named data volume.

## Commands

```bash
docker compose up --build
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
docker compose exec app php artisan test
docker compose exec app php artisan queue:work
docker compose exec app php artisan schedule:work
docker compose exec app npm run build
docker compose exec app npm run dev
```

PostgreSQL data lives in the `postgres_data` named volume. Future raw metadata snapshots use local storage through Laravel's filesystem abstraction.
