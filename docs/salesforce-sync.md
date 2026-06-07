# Salesforce Sync

Stage 1A uses Salesforce CLI for a local metadata probe. OAuth and token storage are intentionally not implemented yet.

## Authenticate Outside GTM Lens

Use Salesforce CLI on the host:

```bash
sf org login web --alias stage --instance-url https://mural--stage.sandbox.my.salesforce.com
```

The Docker app image includes Salesforce CLI. Host CLI config is mounted read-only at `/host/.sf` and `/host/.sfdx`; Docker-managed volumes are mounted at `/root/.sf` and `/root/.sfdx` so Linux file permissions are valid. The probe can reuse the authenticated CLI alias, but GTM Lens does not store Salesforce access tokens or refresh tokens in its database in Stage 1A.

## Import Host CLI Config

After logging in on Windows, import the CLI config into Docker-managed volumes:

```bash
docker compose exec app sh -lc "cp -a /host/.sf/. /root/.sf/ 2>/dev/null || true; cp -a /host/.sfdx/. /root/.sfdx/ 2>/dev/null || true; chmod 700 /root/.sf /root/.sfdx; chmod 600 /root/.sfdx/key.json 2>/dev/null || true"
```

## Run The Probe

```bash
docker compose exec app php artisan salesforce:metadata-probe stage
```

The command creates or updates a CLI placeholder org, creates a `sync_run`, runs allowlisted metadata queries, stores local JSON snapshots, and records counts by metadata type.

Snapshots are stored under:

```text
storage/app/snapshots/{orgAlias}/{syncRunId}/
```


## Run Source Retrieve

Stage 1B retrieves raw SFDX-style metadata source through Salesforce CLI:

```bash
docker compose exec app php artisan salesforce:source-retrieve stage
```

The default manifest is `manifest/gtm-lens-source.xml`. Retrieved source is stored under:

```text
storage/app/snapshots/{orgAlias}/{syncRunId}/source/
```

This command records a `sync_run` with `triggered_by = cli_source_retrieve` and one `metadata_snapshots` row with `snapshot_type = source_retrieve`.


## Normalize Source

Stage 1C normalizes retrieved source into local tables:

```bash
docker compose exec app php artisan salesforce:source-normalize --latest --orgAlias=stage
```

The command creates a new `sync_run` with `triggered_by = source_normalization` and populates:

- `metadata_entities`
- `metadata_entity_versions`
- `search_documents`

Supported Stage 1C source types are Apex classes, Apex triggers, custom objects, custom fields, flows, and validation rules. Dependency edges and dictionary editing remain future work.

## Browse Normalized Metadata

Stage 1D exposes normalized metadata in the app:

```text
http://localhost:8080/search
```

The browser reads from `metadata_entities` and `search_documents`. It supports search, type/status filters, pagination, and a detail page per entity.

## Storage Direction

GTM Lens uses a hybrid metadata storage direction:

- Raw snapshots preserve source-of-truth Salesforce responses and, in later stages, SFDX-style metadata source files.
- Normalized PostgreSQL tables store searchable and reportable metadata extracted from snapshots.

Stage 1A stores raw CLI JSON snapshots only. Stage 1B stores raw SFDX-style source snapshots. Stage 1C populates the first normalized tables for objects, fields, Apex, flows, and validation rules. Later stages should add dependency parsing and dictionary workflows.

## Allowed Metadata Objects

- `EntityDefinition`
- `FieldDefinition`
- `ApexClass`
- `ApexTrigger`
- `ValidationRule`
- `Flow`
- `FlowDefinition`
- `FlowVersionView`
- `CustomObject`
- `CustomField`
- `PicklistValueInfo`

## Forbidden Queries

- `Account`
- `Contact`
- `Opportunity`
- `SELECT *`
- any object not on the allowlist

The MVP must not query or store Salesforce business record data.

## Still Out Of Scope

- Salesforce OAuth
- encrypted token storage
- connected app setup
- refresh token handling
- dependency-aware normalization beyond the Stage 1C source index
- search indexing
- dependency parsing
