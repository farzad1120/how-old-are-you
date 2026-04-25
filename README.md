# How Old Are You

[![CI](https://github.com/farzad1120/how-old-are-you/actions/workflows/ci.yml/badge.svg)](https://github.com/farzad1120/how-old-are-you/actions/workflows/ci.yml)
[![License](https://img.shields.io/badge/license-GPL--2.0--or--later-blue)](LICENSE)
[![PHP](https://img.shields.io/badge/php-%E2%89%A57.4-777bb3)](composer.json)
[![WordPress](https://img.shields.io/badge/wordpress-%E2%89%A56.0-21759b)](readme.txt)

A focused, production-ready age-verification plugin for WordPress. Blocks the public frontend behind a customizable verification gate; remembers verified visitors via a signed cookie.

## Features

- **Two verification modes** — date of birth (age computed in the site timezone) or "I am over X" confirmation. Admin-selectable.
- **Signed cookie** — `payload|expiry|hmac_sha256(secret=wp_salt('auth'))`. Tampered or expired values fail verification, so a visitor cannot forge a pass via browser dev tools.
- **No cookie on failure** — under-age visitors cannot refresh their way past the gate.
- **Standalone gate document** — the page body never reaches an unverified visitor, not even via "view source".
- **Frontend-only** — `wp-admin`, `wp-login.php`, REST, AJAX, cron, XML-RPC, robots/feeds, and any non-GET requests always bypass the gate. Plus admin-configurable path-prefix exclusions.
- **Fully themable** — colors via the WP color picker, logo via the media library, opacity slider, custom CSS, custom strings (with `{age}` interpolation).
- **Translation-ready** — every user-facing string is i18n-ready; `.pot` template ships in `languages/`.
- **Multisite-aware** — uninstall cleans options across the network.
- **Quality-gated** — WordPress Coding Standards (PHPCS), unit + integration tests (PHPUnit), GitHub Actions and GitLab CI on PHP 7.4 / 8.1 / 8.3.

## Quick start

1. Drop the plugin into `wp-content/plugins/how-old-are-you/`.
2. Activate it from **Plugins → Installed Plugins**.
3. Visit **Settings → Age Verification** to configure.

## Configuration

Every setting is in **Settings → Age Verification**. See [`docs/CONFIGURATION.md`](docs/CONFIGURATION.md) for the full reference.

| Group | Settings |
|---|---|
| Behavior | enable, minimum age, verification mode |
| Cookie | name, lifetime (days), SameSite |
| Messages | heading, body, DOB label, yes/no labels, submit label, rejection heading + body |
| Appearance | logo, background color, overlay opacity, panel/text/accent colors, custom CSS |
| Exclusions | path prefixes to bypass |

## Development

This repo ships a Docker dev environment so you don't need PHP locally. See [`docs/DEV_ENVIRONMENT.md`](docs/DEV_ENVIRONMENT.md).

```sh
# Spin up WordPress 6.5 + MariaDB 11
docker compose -f docker-compose.dev.yml up -d
# → http://localhost:8080

# Install dev deps
docker run --rm -v "$PWD":/app -w /app composer:2 install

# Lint
docker run --rm -v "$PWD":/app -w /app composer:2 run lint

# Test
docker run --rm -v "$PWD":/app -w /app composer:2 run test
```

## Hooks reference

See [`docs/DEVELOPER.md`](docs/DEVELOPER.md). Quick summary:

| Hook | Type | Purpose |
|---|---|---|
| `hoay_should_gate` | filter (bool) | Final say on whether the gate renders. |
| `hoay_excluded_paths` | filter (string[]) | Modify the list of bypassed path prefixes. |
| `hoay_verification_result` | filter (array) | Mutate the AJAX response. |
| `hoay_cookie_args` | filter (array) | Adjust cookie flags before `setcookie()`. |

Themes can override the modal markup by placing `how-old-are-you/modal.php` in the active theme.

## Security

See [`docs/SECURITY.md`](docs/SECURITY.md) for the threat model and reporting guidance.

## License

GPL-2.0-or-later. See [`LICENSE`](LICENSE).
