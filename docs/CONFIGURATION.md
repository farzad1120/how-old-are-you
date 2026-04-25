# Configuration reference

All settings live under **Settings → Age Verification** in the WordPress admin. They are stored as a single associative array in the `hoay_settings` option.

## Behavior

| Field | Type | Default | Notes |
|---|---|---|---|
| Enable gate | bool | `true` | Master switch. When off, the gate never renders. |
| Minimum age | int 1–120 | `18` | Visitors below this age cannot pass. |
| Verification mode | radio | `dob` | `dob` (date picker) or `confirm` (yes/no buttons). |

## Cookie

| Field | Type | Default | Notes |
|---|---|---|---|
| Cookie name | slug | `hoay_verified` | Lowercased; alphanumerics, dashes, underscores only. |
| Cookie lifetime (days) | int 1–365 | `30` | How long a verified visitor stays verified. |
| SameSite | enum | `Lax` | `Lax`, `Strict`, or `None`. `None` requires HTTPS. |

The cookie is HMAC-SHA256 signed using `wp_salt('auth')`. Tampered or expired values fail verification and the gate re-renders. The `Secure` flag is set automatically when the request is HTTPS. The cookie is always `HttpOnly`.

## Messages

Every string supports the placeholder `{age}`, which is replaced with the configured **Minimum age** at render time.

| Field | Type | Notes |
|---|---|---|
| Heading | text | Modal H1. |
| Body text | textarea | Optional intro paragraph. |
| DOB field label | text | Shown above the date input in DOB mode. |
| "Over X" button label | text | Confirm mode primary button (e.g. `I am {age} or older`). |
| "Under X" button label | text | Confirm mode secondary button. |
| Submit button label | text | DOB mode submit button. |
| Rejection heading | text | Shown after a failed verification. |
| Rejection body | textarea | Detail under the rejection heading. |

## Appearance

| Field | Type | Notes |
|---|---|---|
| Logo | media library image | Optional, displayed centered above the heading. |
| Background color | hex | Page background behind the panel. |
| Overlay opacity | float 0–1 | Darkens the background. |
| Panel color | hex | Card background. |
| Text color | hex | Primary text color. |
| Accent color | hex | Buttons + focus ring. |
| Custom CSS | textarea | Scoped to the verification document. Tags and obvious script sequences are stripped on save. |

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

## Where to go next

- See [`DEVELOPER.md`](DEVELOPER.md) for the filter and template-override APIs.
- See [`SECURITY.md`](SECURITY.md) for the threat model.
