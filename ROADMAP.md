# GTM Lens Roadmap

This roadmap is directional. Keep implementation gated by explicit stage approval and the security rule that the MVP must not query or store Salesforce business record data.

## Completed

- Stage 0 local Docker foundation.
- Local email/password auth.
- PostgreSQL 17 schema foundation.
- Livewire, Blade, Tailwind UI shell.
- Stage 1A Salesforce CLI metadata probe branch.

## In Progress

- Salesforce CLI metadata probe validation against the real `stage` org.
- Strict metadata query allowlist.
- Local JSON snapshots for probe output.
- Read-only Salesforce org and sync run tables.

## Planned MVP Capabilities

- Stage 1B SFDX-style raw metadata source retrieve through Salesforce CLI.
- Stage 1C metadata normalization into PostgreSQL from raw snapshots/source files.
- Metadata collectors for objects, fields, validation rules, Apex, and flows.
- Metadata search using PostgreSQL full-text search and `pg_trgm`.
- Metadata detail cards.
- Data dictionary with owners, definitions, classification, criticality, lifecycle status, and tags.
- Table-first dependency and impact analysis.
- Change timeline between syncs.
- Basic potential issue detection.
- Audit logs and policies for sensitive actions.
- Salesforce OAuth connection only after the CLI path validates useful metadata and permission boundaries.
- Encrypted Salesforce token storage only when OAuth is intentionally introduced.

## Storage Direction

GTM Lens should keep both forms of metadata:

- Raw snapshots/source files preserve the canonical Salesforce metadata shape and support future diffs and re-parsing.
- Normalized PostgreSQL tables power search, UI, dictionary workflows, dependency analysis, reporting, and comparisons.

The database should be an interpreted index of the raw snapshot, not the only copy of metadata.

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
