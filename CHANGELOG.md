# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.0.0] - 2026-04-25

### Added
- Initial public release.
- Age-verification gate for the public WordPress frontend with two admin-selectable modes:
  - **DOB mode** — visitor enters date of birth; age is computed in the site timezone.
  - **Confirm mode** — visitor clicks "I am over X" / "I am under X".
- Signed (HMAC-SHA256) verification cookie with admin-configurable name, lifetime (1–365 days), and SameSite policy.
- Standalone overlay document so the page body is never served to unverified visitors.
- Always-exempt request types: admin, REST, AJAX, cron, XML-RPC, login/register, non-GET, robots/feeds.
- Admin-configurable path-prefix exclusions.
- Full theming via the admin UI: logo, background/panel/text/accent colors, overlay opacity, custom CSS.
- All user-facing strings configurable, with `{age}` placeholder interpolation.
- Translation-ready (`.pot` template, `load_plugin_textdomain`).
- Multisite-aware uninstall.
- Filters for extensibility: `hoay_should_gate`, `hoay_excluded_paths`, `hoay_verification_result`, `hoay_cookie_args`.
- Theme override for `templates/modal.php`.
- WordPress Coding Standards (PHPCS) configuration.
- PHPUnit unit suite (27 tests) and integration scaffold (skipped without `WP_TESTS_DIR`).
- GitHub Actions and GitLab CI pipelines on PHP 7.4 / 8.1 / 8.3.
- Docker dev environment (`docker-compose.dev.yml`) with WordPress + MariaDB + WP-CLI.

[Unreleased]: https://github.com/farzadzarasvand/how-old-are-you/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/farzadzarasvand/how-old-are-you/releases/tag/v1.0.0
