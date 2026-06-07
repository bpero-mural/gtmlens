# Salesforce Sync

Stage 1A uses Salesforce CLI for a local metadata probe. OAuth and token storage are intentionally not implemented yet.

## Authenticate Outside Mural Lens

Use Salesforce CLI on the host or inside an environment where `sf` is available:

```bash
sf org login web --alias stage
```

Mural Lens does not store Salesforce access tokens or refresh tokens in Stage 1A.

## Run The Probe

```bash
docker compose exec app php artisan salesforce:metadata-probe stage
```

The command creates or updates a CLI placeholder org, creates a `sync_run`, runs allowlisted metadata queries, stores local JSON snapshots, and records counts by metadata type.

Snapshots are stored under:

```text
storage/app/snapshots/{orgAlias}/{syncRunId}/
```

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
- metadata normalization beyond local snapshots
- search indexing
- dependency parsing
