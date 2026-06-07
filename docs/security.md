# Security

Stage 0 is local-only. Stage 1A can read allowlisted metadata through Salesforce CLI, but it does not implement OAuth or store Salesforce tokens.

## Non-Negotiable MVP Boundary

The MVP must not read Salesforce business record data. It may only retrieve approved metadata in later stages. Forbidden examples include Account, Contact, Opportunity, or any object rows containing business values.

Stage 1A must block `Account`, `Contact`, `Opportunity`, `SELECT *`, and any object not explicitly allowlisted for metadata probing.

## Sensitive Data Rules

- Do not log Salesforce tokens.
- Do not log Authorization headers.
- Do not log raw Apex bodies, Flow XML, Named Credential secrets, or business record values.
- Raw metadata viewing must remain permission-gated in later stages.
- `SECURITY_ALLOW_APEX_STORAGE=false` by default.
- `SECURITY_ALLOW_RAW_METADATA_VIEW=false` by default.

## Local Auth

Stage 0 uses local Laravel auth with a seeded admin user for development only.
