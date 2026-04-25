# Configuration reference

All settings live under **Settings → Age Verification** in the WordPress admin. They are stored as a single associative array in the `hoay_settings` option.

## Behavior

| Field | Type | Default | Notes |
|---|---|---|---|
| Enable gate | bool | `true` | Master switch. When off, the gate never renders. |
| Minimum age | int 1–120 | `18` | Visitors below this age cannot pass. |
| Verification mode | radio | `dob` | `dob` (date picker) or `confirm` (yes/no buttons). |
| DOB input style | radio | `native` | `native` (HTML5 picker, follows the document `lang`) or `selects` (three site-localized dropdowns). |

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

The Appearance section is split into six subsections in the admin UI.

### Logo

| Field | Type | Default | Notes |
|---|---|---|---|
| Logo | media library image | none | Optional, displayed centered above the heading. |
| Logo max width (px) | int 40–400 | `160` | Caps the rendered logo width. |

### Background

| Field | Type | Default | Notes |
|---|---|---|---|
| Background color | hex | `#0b0b0b` | Page background behind the panel. |
| Background image | media library image | none | Optional image laid over the background color. |
| Background image size | enum | `cover` | `cover`, `contain`, or `auto` (CSS `background-size`). |
| Overlay opacity | float 0–1 | `0.92` | Darkens whatever is behind the panel. |
| Backdrop blur (px) | int 0–32 | `0` | Frosted-glass effect via `backdrop-filter: blur()`. |

### Panel

| Field | Type | Default | Notes |
|---|---|---|---|
| Panel color | hex | `#ffffff` | Card background. |
| Panel width (px) | int 320–720 | `440` | Maximum width of the card. |
| Panel padding (px) | int 16–64 | `36` | Inner spacing. |
| Panel border radius (px) | int 0–32 | `12` | |

### Typography

| Field | Type | Default | Notes |
|---|---|---|---|
| Font family | text | empty | CSS font stack. Empty falls back to a system stack. |
| Body font size (px) | int 12–24 | `16` | |
| Heading font size (px) | int 16–48 | `22` | |
| Text color | hex | `#111111` | |
| Text alignment | enum | `center` | `left`, `center`, or `right`. |

### Controls

| Field | Type | Default | Notes |
|---|---|---|---|
| Accent color | hex | `#c7a008` | Primary button background and focus ring. |
| Button border radius (px) | int 0–32 | `8` | |
| Input border radius (px) | int 0–32 | `8` | |

### Custom CSS

A textarea scoped to the verification overlay. Tags and obvious script sequences are stripped on save (`<`, `>`, `javascript:`, `expression(`, `behavior:`, `@import`).

Every theming setting above is exposed as a CSS custom property, so Custom CSS can compose with them:

```
--hoay-bg              --hoay-text
--hoay-bg-image        --hoay-text-align
--hoay-bg-size         --hoay-accent
--hoay-opacity         --hoay-font
--hoay-blur            --hoay-font-size
--hoay-panel           --hoay-heading-size
--hoay-panel-width     --hoay-button-radius
--hoay-panel-padding   --hoay-input-radius
--hoay-panel-radius    --hoay-logo-max
```

## SEO

The verification gate blocks the page body for unverified visitors. Without these settings, that means Googlebot and social-media link unfurlers would see the verification overlay instead of your content. The SEO section makes the gate friendly to crawlers while keeping the gate intact for human visitors.

| Field | Type | Default | Notes |
|---|---|---|---|
| Bypass for crawlers | bool | `true` | When on, requests with a UA matching the configured list bypass the gate entirely and see the real page. |
| Crawler user agents | textarea (one per line) | empty (uses built-in list) | Case-insensitive substring matches. The built-in list covers Googlebot, Bingbot, Slurp, DuckDuckBot, Baiduspider, YandexBot, Sogou, Applebot, facebookexternalhit, Twitterbot, LinkedInBot, Pinterestbot, TelegramBot, Discordbot, Slackbot, WhatsApp, ia_archiver, AdsBot-Google, and more. |
| Robots meta | text | `noindex,nofollow` | Comma-separated tokens. Allowed: `index`, `noindex`, `follow`, `nofollow`, `noarchive`, `nosnippet`, `noimageindex`, `none`, `all`. |
| Canonical URL | bool | `true` | Emit `<link rel="canonical">` pointing to the requested URL on the overlay. |
| Inherit Open Graph | bool | `true` | When the resolved request maps to a `WP_Post` or `WP_Term`, derive `og:title` from it. Featured image is used for `og:image` if no fallback is configured. `og:type` becomes `article` for posts. |
| Meta description | textarea | empty | Falls back to the gate body text when empty. |
| Open Graph image | media library | none | Fallback `og:image` (recommended 1200×630). |

The overlay always emits `og:site_name` and `twitter:card="summary_large_image"`.

### Filters

| Hook | Type | Purpose |
|---|---|---|
| `hoay_bot_tokens` | filter (string[]) | Modify the effective bot-token list per request. |
| `hoay_is_search_bot` | filter (bool) | Final override of the bot decision. |

### Tradeoff

Letting bots through means a visitor can also bypass the gate by spoofing their User-Agent (e.g. setting it to `Googlebot`). This is consistent with how every age-gate plugin works: enforcement is best-effort and reflects the same content a search engine would index. If your compliance regime forbids any bot bypass, disable the **Bypass for crawlers** checkbox — Googlebot will then see the verification overlay and the site will not be indexed.

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
