=== How Old Are You ===
Contributors: farzadzarasvand
Tags: age verification, age gate, compliance, alcohol, cannabis
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.2.0
License: GPL-2.0-or-later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Block under-age visitors from your public WordPress site with a customizable, signed-cookie age verification gate.

== Description ==

How Old Are You is a focused, production-ready age-verification plugin for WordPress. It blocks the public frontend behind a configurable verification screen, and remembers verified visitors via a signed (HMAC-SHA256) cookie so they only have to confirm once.

= Features =

* **Two verification modes (admin-configurable)**
  * **Date of birth** — visitor enters their DOB; the plugin computes their age in the site's timezone and compares it to the configured minimum.
  * **Confirmation** — visitor clicks "I am over X" or "I am under X". Lower friction; still server-validated.
* **Configurable minimum age** (1–120, default 18).
* **Signed cookie** with admin-configurable name, lifetime (1–365 days, default 30), and SameSite policy. Tampered or expired cookies fail verification.
* **No cookie on failure** — under-age visitors cannot bypass by refreshing.
* **Standalone gate document** — when an unverified visitor lands on a public page, the plugin streams a complete HTML document and exits before WordPress generates the page body. The site's HTML is never served to under-age visitors, even via "view source".
* **Always exempts** wp-admin, wp-login.php, REST, AJAX, cron, XML-RPC, robots/feeds, and any non-GET requests. Plus admin-configurable path prefixes.
* **Full theming** — background, panel, text, accent colors via the WordPress color picker; logo from the media library; overlay opacity slider; custom CSS textarea.
* **Translation-ready** — every string wrapped in `__()` with the `how-old-are-you` text domain; `.pot` template ships in `languages/`.
* **Multisite-aware** — uninstall removes options across the network.

= Filters =

* `hoay_should_gate` — short-circuit the gate (return false to skip rendering).
* `hoay_excluded_paths` — modify the list of excluded path prefixes.
* `hoay_verification_result` — mutate the AJAX response before it's sent.
* `hoay_cookie_args` — adjust cookie flags before they hit `setcookie()`.

= Template overrides =

Drop a copy of `templates/modal.php` into your theme as `<theme>/how-old-are-you/modal.php` to take full control of the overlay markup.

== Installation ==

1. Upload the plugin zip via **Plugins → Add New → Upload**, or unpack into `wp-content/plugins/`.
2. Activate it under **Plugins → Installed Plugins**.
3. Configure under **Settings → Age Verification**.

== Frequently Asked Questions ==

= Does this stop a determined visitor from viewing my site? =

It stops *passive* circumvention: the page body never reaches the browser, the cookie is HMAC-signed so it cannot be forged in dev tools, and the under-age path never sets a cookie. A determined visitor can still type any DOB. This plugin is a compliance control, not a DRM system.

= Will my site still get indexed by search engines? =

Yes. Bots that don't accept cookies will see the verification overlay, but the overlay carries `<meta name="robots" content="noindex,nofollow">` so it isn't indexed. Robots.txt and feeds are exempt from the gate, and you can add additional exempt path prefixes from the settings page.

= Can I gate only some pages? =

The gate covers all public pages by default. To carve out exceptions, list path prefixes (one per line) in **Excluded paths**. To gate *only* specific pages, use the `hoay_should_gate` filter with your own logic.

= Where is verification state stored? =

In a single signed cookie (`hoay_verified` by default). Nothing is stored server-side per visitor.

== Screenshots ==

1. The settings page under Settings → Age Verification.
2. The verification overlay (DOB mode).
3. The verification overlay (confirm mode).
4. The under-age rejection panel.

== Changelog ==

= 1.2.0 =

* SEO controls — search engines and social-media unfurlers (Googlebot, Bingbot, Twitterbot, facebookexternalhit, LinkedInBot, etc.) bypass the gate by default so the real page is indexed and link previews work.
* Configurable user-agent token list — admins can add, remove, or replace the built-in bot list.
* Configurable robots meta tag on the verification overlay (default `noindex,nofollow`, with full token validation).
* Optional canonical URL on the overlay pointing to the originally requested URL.
* Open Graph / Twitter Card tags on the overlay; when enabled, og:title/og:image inherit from the resolved post (with featured image) or a configurable fallback OG image.
* Optional meta description override; falls back to the gate body text.

= 1.1.0 =

* New theming options: font family, body and heading sizes, panel width/padding/border-radius, button/input border-radius, backdrop blur, background image (with cover/contain/auto sizing), logo max width, text alignment.
* Every theming setting is now exposed as a CSS custom property on the overlay so Custom CSS can lean on the same tokens.
* DOB input follows the WordPress site locale: localized format hint based on `date_format`, `lang` attribute on the native picker, and an optional dropdown-selects mode that renders day/month/year using `date_i18n()` for site-localized month names.
* Improved admin Appearance section: split into Logo / Background / Panel / Typography / Controls / Custom CSS subsections.

= 1.0.0 =

* Initial release.

== Upgrade Notice ==

= 1.2.0 =

Adds SEO controls so search engines and social-media link previews see the real page instead of the verification gate. Backward compatible — existing settings keep working.

= 1.1.0 =

Adds many new theming options and a site-locale-aware DOB input. Backward compatible — existing settings keep working unchanged.

= 1.0.0 =

First release.
