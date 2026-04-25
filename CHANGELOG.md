# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 2026-04-25

### Added
- New theming settings: `font_family`, `font_size_base_px`, `heading_size_px`, `panel_width_px`, `panel_padding_px`, `panel_radius_px`, `button_radius_px`, `input_radius_px`, `backdrop_blur_px`, `background_image_id`, `background_image_size`, `logo_max_width_px`, `text_align`.
- Every theming setting is exposed as a CSS custom property on `.hoay-overlay`, so Custom CSS can compose with them (e.g. `--hoay-accent`, `--hoay-panel-radius`, `--hoay-font`).
- Optional `dob_input_style: 'selects'` mode that renders three site-localized day/month/year dropdowns. Month names come from `date_i18n('F', ...)` so they always follow the WordPress locale regardless of the visitor's browser language.
- Native HTML5 date input now carries a `lang` attribute matching the site language, and a localized format hint derived from the `date_format` option.
- `Sanitizer::font_family()` plus three new enum constants (`DOB_INPUT_STYLES`, `TEXT_ALIGNMENTS`, `BACKGROUND_SIZES`).

### Changed
- Admin Appearance section split into Logo / Background / Panel / Typography / Controls / Custom CSS subsections, each with its own table.
- Frontend overlay redrawn against the new CSS variables; backdrop blur is now a `::before` pseudo-element so it composes correctly with the panel.

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

[Unreleased]: https://github.com/farzadzarasvand/how-old-are-you/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/farzadzarasvand/how-old-are-you/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/farzadzarasvand/how-old-are-you/releases/tag/v1.0.0
