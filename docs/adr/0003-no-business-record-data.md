# ADR 0003: No Business Record Data

## Status

Accepted.

## Decision

The MVP must not query or store Salesforce business record data.

## Consequences

Later Salesforce query code must use allowlists and tests to block business objects such as Account, Contact, and Opportunity rows.
