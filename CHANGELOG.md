# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2026-04-26

Initial public release.

### Verification

- Age-verification gate for the public WordPress frontend with two admin-selectable modes:
  - **DOB mode** — visitor enters date of birth; age is computed in the site timezone.
  - **Confirm mode** — visitor clicks "I am over X" / "I am under X".
- Configurable minimum age (1–120, default 18).
- Site-locale-aware DOB input: localized format hint based on the site's `date_format`, `lang` attribute on the native HTML5 picker, and an optional dropdown-selects style with day/month/year selectors whose month names follow the WordPress locale via `date_i18n()`.
- HMAC-SHA256-signed verification cookie with admin-configurable name, lifetime (1–365 days), and SameSite policy. Tampered or expired cookies fail verification.
- No cookie is written on a failed verification, so under-age visitors cannot bypass by refreshing.
- Standalone overlay document — the page body is never served to unverified visitors, even via "view source".

### Customization

- All user-facing strings configurable, with `{age}` placeholder interpolation.
- Full theming via the admin UI:
  - Logo (media library) and logo max width.
  - Background color, optional background image, `cover`/`contain`/`auto` sizing, overlay opacity, backdrop blur.
  - Panel color, width, padding, border radius.
  - Typography: font family, body and heading font sizes, text color, text alignment.
  - Accent color, button border radius, input border radius.
  - Custom CSS textarea, with every theming token exposed as a CSS custom property.

### Crawlers

- Bot bypass with admin-configurable user-agent list and a built-in default list covering major search engines (Googlebot, Bingbot, Slurp, DuckDuckBot, Baiduspider, YandexBot, …) and social-media unfurlers (facebookexternalhit, Twitterbot, LinkedInBot, Pinterestbot, TelegramBot, Discordbot, Slackbot, WhatsApp, …).
- Always-exempt request types: admin, REST, AJAX, cron, XML-RPC, login/register, non-GET, robots/feeds/trackbacks. Plus admin-configurable path-prefix exclusions.

### Quality

- Translation-ready (`.pot` template, `load_plugin_textdomain`, `how-old-are-you` text domain).
- Multisite-aware uninstall.
- Filters for extensibility: `hoay_should_gate`, `hoay_excluded_paths`, `hoay_verification_result`, `hoay_cookie_args`, `hoay_bot_tokens`, `hoay_is_search_bot`.
- Theme override for `templates/modal.php`.
- WordPress Coding Standards (PHPCS) configuration.
- PHPUnit unit suite (60 tests covering age math, sanitisation, cookie HMAC, bot detection, and every CSS variable rendered to the overlay) plus an integration scaffold guarded by `WP_TESTS_DIR`.
- GitHub Actions and GitLab CI pipelines on PHP 7.4 / 8.1 / 8.3.
- Docker dev environment (`docker-compose.dev.yml`) with WordPress + MariaDB + WP-CLI for end-to-end manual testing without local PHP.

[Unreleased]: https://github.com/farzad1120/how-old-are-you/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/farzad1120/how-old-are-you/releases/tag/v1.0.0
