# Security

Stage 0 is local-only and does not connect to Salesforce.

## Non-Negotiable MVP Boundary

The MVP must not read Salesforce business record data. It may only retrieve approved metadata in later stages. Forbidden examples include Account, Contact, Opportunity, or any object rows containing business values.

## Sensitive Data Rules

- Do not log Salesforce tokens.
- Do not log Authorization headers.
- Do not log raw Apex bodies, Flow XML, Named Credential secrets, or business record values.
- Raw metadata viewing must remain permission-gated in later stages.
- `SECURITY_ALLOW_APEX_STORAGE=false` by default.
- `SECURITY_ALLOW_RAW_METADATA_VIEW=false` by default.

## Local Auth

Stage 0 uses local Laravel auth with a seeded admin user for development only.
