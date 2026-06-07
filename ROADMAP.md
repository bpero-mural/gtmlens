# GTM Lens Roadmap

This roadmap is directional. Keep implementation gated by explicit stage approval and the security rule that the MVP must not query or store Salesforce business record data.

## Completed

- Stage 0 local Docker foundation.
- Local email/password auth.
- PostgreSQL 17 schema foundation.
- Livewire, Blade, Tailwind UI shell.
- Stage 1A Salesforce CLI metadata probe branch.

## In Progress

- Salesforce CLI metadata probe.
- Strict metadata query allowlist.
- Local JSON snapshots for probe output.
- Read-only Salesforce org and sync run tables.

## Planned MVP Capabilities

- Salesforce OAuth connection after the CLI probe validates useful metadata.
- Encrypted Salesforce token storage.
- Manual sync command for connected orgs.
- Metadata collectors for objects, fields, validation rules, Apex, and flows.
- Metadata normalization into PostgreSQL.
- Metadata search using PostgreSQL full-text search and `pg_trgm`.
- Metadata detail cards.
- Data dictionary with owners, definitions, classification, criticality, lifecycle status, and tags.
- Table-first dependency and impact analysis.
- Change timeline between syncs.
- Basic potential issue detection.
- Audit logs and policies for sensitive actions.

## Future Capabilities

- Reports and dashboards metadata.
- Layouts and FlexiPages.
- Profiles and Permission Sets.
- Setup Audit Trail import.
- Connected Apps inventory.
- EventLogFile/API monitoring.
- Saved views and internal share links.
- Azure deployment.
- Microsoft Entra ID SSO.
- Optional approved AI features.
- Aggregate-only field usage scanner with strict allowlists and no raw values.

## Explicitly Not Planned For MVP

- Salesforce business record data ingestion.
- External AI or external design SaaS.
- Redis, OpenSearch, Elasticsearch, Neo4j, graph databases, or SaaS search.
- Vue, Inertia, Nuxt UI, graph visualization libraries, paid UI kits, or marketplace blocks.
