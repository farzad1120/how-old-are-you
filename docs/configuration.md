---
title: Configuration
nav_order: 3
permalink: /configuration/
---

# Configuration

All settings live under **Settings → Age Verification** in the WordPress admin. They are stored as a single associative array in the `hoay_settings` option.

## Behavior

| Field | Type | Default | Notes |
|---|---|---|---|
| Enable gate | bool | `true` | Master switch. When off, the gate never renders. |
| Minimum age | int 1–120 | `18` | Visitors below this age cannot pass. |
| Verification mode | radio | `dob` | `dob` (date picker) or `confirm` (yes/no buttons). |
| DOB input style | radio | `native` | `native` (HTML5 picker, follows document `lang`) or `selects` (three site-localized dropdowns). |

## Cookie

| Field | Type | Default | Notes |
|---|---|---|---|
| Cookie name | slug | `hoay_verified` | Lowercased; alphanumerics, dashes, underscores only. |
| Cookie lifetime (days) | int 1–365 | `30` | How long a verified visitor stays verified. |
| SameSite | enum | `Lax` | `Lax`, `Strict`, or `None`. `None` requires HTTPS. |

The cookie is HMAC-SHA256 signed using `wp_salt('auth')`. Tampered or expired values fail verification and the gate re-renders. The `Secure` flag is set automatically when the request is HTTPS. The cookie is always `HttpOnly`.

## Messages

Every string supports the placeholder `{age}`, replaced with the configured **Minimum age** at render time.

| Field | Type | Notes |
|---|---|---|
| Heading | text | Modal H1. |
| Body text | textarea | Optional intro paragraph. |
| DOB field label | text | Shown above the date input in DOB mode. |
| "Over X" button label | text | Confirm-mode primary button. |
| "Under X" button label | text | Confirm-mode secondary button. |
| Submit button label | text | DOB-mode submit button. |
| Rejection heading | text | Shown after a failed verification. |
| Rejection body | textarea | Detail under the rejection heading. |

## Crawlers

Search engines and social-media unfurlers should see the real page, not the verification overlay, so the site stays indexable and link previews work. The age gate stays in place for human visitors.

| Field | Type | Default | Notes |
|---|---|---|---|
| Bypass for crawlers | bool | `true` | When on, requests with a UA matching the configured list bypass the gate entirely. |
| Crawler user agents | textarea (one per line) | empty (uses built-in list) | Case-insensitive substring matches. The built-in list covers Googlebot, Bingbot, Slurp, DuckDuckBot, Baiduspider, YandexBot, Sogou, Applebot, facebookexternalhit, Twitterbot, LinkedInBot, Pinterestbot, TelegramBot, Discordbot, Slackbot, WhatsApp, ia_archiver, AdsBot-Google, and more. |

The overlay always emits `<meta name="robots" content="noindex,nofollow">` so the gate page itself isn't indexed.

### Tradeoff

Letting bots through means a visitor can also bypass the gate by spoofing their User-Agent (e.g. setting it to `Googlebot`). This is consistent with how every age-gate plugin works. If your compliance regime forbids any bot bypass, disable **Bypass for crawlers** — Googlebot will then see the verification overlay and the site will not be indexed.

## Exclusions

**Excluded paths** is a textarea, one path per line. Any request whose path *starts with* one of the listed prefixes bypasses the gate.

```
/privacy
/contact
/about
```

In addition, the following always bypass the gate (not configurable):

- `wp-admin/*`
- `wp-login.php`, `wp-register.php`
- REST (`/wp-json/...`)
- AJAX (`admin-ajax.php`)
- Cron (`wp-cron.php`)
- XML-RPC (`xmlrpc.php`)
- robots.txt, feeds, trackbacks
- Any non-GET request method

## Where to next

- [Customize the look]({{ "/customization/" | relative_url }}) — colors, custom CSS, sample snippet.
- [Troubleshooting]({{ "/troubleshooting/" | relative_url }}) — common issues.
- [For developers]({{ "/developer/" | relative_url }}) — filters and template overrides.
