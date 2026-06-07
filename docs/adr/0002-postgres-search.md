# ADR 0002: PostgreSQL Search

## Status

Accepted.

## Decision

Use PostgreSQL full-text search plus `pg_trgm` and `unaccent` instead of external search infrastructure.

## Consequences

The MVP avoids OpenSearch, Elasticsearch, Redis, and SaaS search dependencies.
