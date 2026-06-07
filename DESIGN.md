---
version: "alpha"
name: "GTM Lens Design System"
description: "Internal Salesforce metadata application for GTM teams."
colors:
  canvas: "#f5f7fb"
  surface: "#ffffff"
  surface-subtle: "#f8fafc"
  border: "#dbe3ef"
  border-strong: "#b8c4d6"
  text: "#0f172a"
  text-muted: "#475569"
  text-subtle: "#64748b"
  primary: "#2563eb"
  primary-hover: "#1d4ed8"
  on-primary: "#ffffff"
  teal: "#0f766e"
  amber: "#b45309"
  danger: "#b91c1c"
  success: "#15803d"
typography:
  body:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "14px"
    fontWeight: "400"
    lineHeight: "1.5"
    letterSpacing: "0"
  h1:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "22px"
    fontWeight: "600"
    lineHeight: "1.25"
    letterSpacing: "0"
  h2:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "16px"
    fontWeight: "600"
    lineHeight: "1.35"
    letterSpacing: "0"
  label:
    fontFamily: "Instrument Sans, ui-sans-serif, system-ui, sans-serif"
    fontSize: "12px"
    fontWeight: "600"
    lineHeight: "1.4"
    letterSpacing: "0.04em"
rounded:
  control: "6px"
  panel: "8px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "16px"
  lg: "24px"
  xl: "32px"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.on-primary}"
    rounded: "{rounded.control}"
    height: "40px"
    padding: "0 16px"
  card:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.text}"
    rounded: "{rounded.panel}"
    padding: "20px"
---

# GTM Lens Design System

## Overview

GTM Lens is an internal Salesforce metadata application. Its interface should feel like an operational cockpit for architects and developers, not a marketing site.

The design is owned by GTM Lens. It may use the open DESIGN.md format as a file structure, but it must not copy Google's visual identity, Google Stitch output, or third-party brand systems.

## Colors

Use a pale blue-gray canvas, white surfaces, slate text, and blue as the primary action color. Use teal for healthy or ready states, amber for pending or warning states, and red for risk.

Avoid one-note purple, beige, dark-blue-only, or decorative gradient palettes.

## Typography

Use Instrument Sans. Keep dashboard and admin surfaces compact. Use uppercase only for small metadata labels, never for large blocks of text. Letter spacing must be zero for normal text.

## Layout

- Prefer a persistent sidebar for main navigation on desktop.
- Keep page headers compact and information-rich.
- Use cards for individual repeated items, stat modules, empty states, and focused panels.
- Do not nest cards.
- Avoid landing-page composition inside the authenticated app.
- Favor tables, lists, tabs, filters, and detail panels over decorative layouts.

## Elevation & Depth

Use subtle borders and very low shadows. The app should feel calm and durable rather than glossy.

## Shapes

Use 6px radius for controls and 8px radius for panels. Avoid oversized pill styling unless a control is genuinely a compact badge.

## Components

- Buttons: 40px height, 6px radius, visible focus ring.
- Inputs: white surface, slate border, blue focus.
- Cards: 8px radius, subtle border, very low shadow.
- Badges: compact, semantic, used for state and type.
- Tables: dense but readable, with strong headers and clear row separation.

## Do's and Don'ts

- Do keep Salesforce metadata work table-first and scan-friendly.
- Do preserve the MVP rule: no Salesforce business record data.
- Do use the local `DESIGN.md` as the product design source of truth.
- Do not introduce Vue, Inertia, UI kits, graph libraries, or decorative design dependencies in the MVP.
- Do not use Google, IBM, Airtable, ClickHouse, Salesforce, or other corporate marks or trade dress unless explicitly approved by the owner.
- Do not use Google Stitch or any external SaaS with screenshots, metadata, code, credentials, or internal product details unless that data transfer is explicitly approved.
- Do not claim affiliation with Google or that this product is built with Google Stitch.
