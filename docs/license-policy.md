# License Policy

Dependencies should be permissive and operationally simple.

## Allowed By Default

- MIT
- BSD
- ISC
- Apache 2.0
- PostgreSQL-like

## Requires Explicit Approval

- GPL
- LGPL
- AGPL
- SSPL
- BSL
- Elastic License
- source-available
- free-for-non-commercial
- SaaS SDKs that send data outside the approved company boundary

## Disallowed In The MVP

- Elasticsearch
- OpenSearch
- Redis
- Valkey
- Neo4j
- Typesense
- Memgraph
- Meilisearch
- Algolia
- SaaS AI
- SaaS observability receiving raw metadata
- Pro packages, paid templates, and marketplace blocks

## Stage 0 Important Dependencies

| Package | Purpose | License | Reason |
| --- | --- | --- | --- |
| `laravel/framework` | Application framework | MIT | Core Laravel app foundation |
| `livewire/livewire` | Server-driven UI components | MIT | Laravel-native interactivity without Vue/Inertia |
| `laravel/tinker` | Local REPL | MIT | Developer convenience |
| `tailwindcss` | Utility CSS | MIT | Styling for Blade components |
| `@tailwindcss/vite` | Tailwind Vite integration | MIT | Asset build integration |
| `laravel-vite-plugin` | Laravel Vite integration | MIT | Laravel asset pipeline |
| `vite` | Frontend build tool | MIT | Asset build and dev server |
| `phpunit/phpunit` | Test runner | BSD-3-Clause | Feature and unit test execution |
