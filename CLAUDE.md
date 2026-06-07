# Mural Lens Agent Instructions

Claude should read this file, but `AGENTS.md` is the cross-agent entrypoint and should be kept in sync with this file.

## Current Stage

Stage 0 is complete and published at `https://github.com/bpero-mural/murallens`.

Do not start Salesforce OAuth, metadata collectors, sync logic, search implementation, or parser work unless the user explicitly requests the next stage.

## Authentication Boundary

Keep authentication simple in Stage 0: local email/password login only. Do not add social login, Microsoft Entra ID, SSO, OAuth UI, or identity-provider packages until the user explicitly starts that work.

Local login:

- URL: `http://localhost/login`
- Email: `admin@example.test`
- Password: `password`

The local admin is repaired by `php artisan mural:ensure-local-admin` and by the local login flow if the user is missing or has a stale hash.

## Design Source Of Truth

Use `DESIGN.md` as the design-system contract for Mural Lens UI work. It is a local, Mural Lens-owned design direction.

The project may reference Google's open DESIGN.md format as a structure for design tokens and rationale. Do not copy Google visual identity, Google Stitch output, Google branding, or third-party brand systems.

## Google DESIGN.md Legal And Vendor Boundary

Google's public materials state that DESIGN.md is an open-source draft specification intended to work across tools and platforms. The public GitHub repository identifies the project as Apache-2.0 licensed.

For Mural Lens:

- Allowed: hand-authored local `DESIGN.md` files that describe Mural Lens-owned colors, typography, spacing, and UI rules.
- Allowed with review: adding Google's `@google/design.md` CLI only if its license is documented, it is locked in `package-lock.json`, and it runs locally without sending data outside the machine.
- Not allowed in the MVP: uploading screenshots, Salesforce metadata, source code, credentials, or internal product details to Google Stitch or any external design SaaS.
- Not allowed: claiming Mural Lens is endorsed by, affiliated with, or built by Google.
- Not allowed: using Google logos, Google product names as branding, Material/Google trade dress, or Stitch-generated output without explicit approval.

This is an engineering compliance note, not legal advice. If Mural plans external distribution, marketing use, or procurement of Google design tooling, get legal/procurement review first.

## Mural Brand Boundary

Mural Lens is an internal application name. Do not introduce Mural corporate logos, proprietary brand assets, or external-facing claims unless the user provides approved assets and usage rules.

## Third-Party Design References

Design references from catalogs such as getdesign.md are inspiration only. They may inform broad qualities like "structured enterprise UI" or "data-dense operational surface", but do not copy brand-specific colors, layouts, logos, names, or distinctive trade dress.

## MVP UI Rules

- Keep the frontend Laravel-native: Blade, Livewire, Tailwind CSS, and custom Blade components.
- Do not add Vue, Inertia, Nuxt UI, PrimeVue, Filament, Flux, Mary UI, DaisyUI, AG Grid, Cytoscape.js, graph visualization libraries, paid UI kits, or marketplace blocks.
- Favor dense, organized, table-first operational UI over marketing composition.
- Do not use decorative gradient backgrounds, orbs, bokeh, or brand-copycat styling.
- Do not expose raw sensitive metadata as a default UI pattern.

## Security Boundary

The MVP must not query or store Salesforce business record data. Do not send Salesforce metadata, credentials, code, screenshots, or internal docs to external SaaS unless the user explicitly approves that data transfer and the security docs are updated.
