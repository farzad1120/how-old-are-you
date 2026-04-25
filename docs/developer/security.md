---
title: Security
parent: For developers
nav_order: 4
permalink: /developer/security/
---

# Security

## Threat model

This plugin is a compliance control, not a DRM system. It assumes the visitor's browser is reasonably honest about cookies and that the site owner controls the WordPress installation. Within those assumptions, the following threats are mitigated:

| Threat | Mitigation |
|---|---|
| **Underage visitor refreshes after entering a too-young DOB** | No cookie is written on failure. Each refresh re-renders the gate. |
| **Visitor forges a verified cookie via dev tools** | Cookie value is `payload\|expiry\|hmac_sha256(payload.expiry, wp_salt('auth'))`. Forged values fail HMAC verification. |
| **Visitor copies an old HMAC and pushes the expiry forward** | The HMAC is keyed off the expiry, so changing the expiry invalidates the HMAC. |
| **Visitor crafts a payload with a far-future expiry** | The HMAC must match the new expiry, which requires `wp_salt('auth')`. |
| **Visitor "view source" to peek at the gated page** | The page body is never streamed for unverified visitors — `Gate::maybe_render()` calls `exit;` after the overlay. |
| **Cross-site request forgery on the verify endpoint** | `check_ajax_referer('hoay_verify')` validates a nonce on every submission. |
| **Reflected XSS via custom CSS / messages** | All user-facing strings are escaped on render (`esc_html`, `esc_attr`, `esc_url`). Custom CSS is stripped of `<`, `>`, and obvious script sequences on save. |
| **Stored XSS via settings** | Capability check `manage_options` plus the Settings API's nonce gate every save; values are sanitised through `HOAY\Support\Sanitizer`. |
| **Visitor spoofs a bot UA to bypass the gate** | Same content shown to a verified human is shown to bots; UA-spoofing self-verifies but doesn't expose privileged content. Site owners with strict requirements can disable bot bypass entirely. |

## Out of scope

- A determined visitor who lies about their DOB. This is true of every age gate. The plugin records *that* a visitor verified, not *what* they entered.
- A visitor who disables JavaScript and submits the form via `<form>` POST directly. The server-side AJAX handler still validates the nonce and applies the same checks; the experience is degraded (no rejection panel) but the security guarantee holds.
- Network attackers without TLS. Run your site on HTTPS.

## Reporting a vulnerability

Please email security reports to **farzad@wedevelop.nl** with subject prefix `[HOAY security]`. Do not file public issues for vulnerabilities. We aim to acknowledge within 5 business days.

When reporting, please include:

- A description of the issue.
- Steps to reproduce, ideally on a clean WordPress install with default settings.
- Affected plugin version.
- Your assessment of severity, if you have one.
