---
title: Architecture
parent: For developers
nav_order: 1
permalink: /developer/architecture/
---

# Architecture

```
how-old-are-you.php           Plugin bootstrap (header, constants, autoload, plugins_loaded).
src/Plugin.php                Singleton orchestrator. Wires sub-components on plugins_loaded.
src/Activator.php             Activation: seed defaults, stamp version.
src/Deactivator.php           No-op (settings persist across deactivation).
src/Settings/Options.php      Typed accessor for the hoay_settings option.
src/Settings/SettingsPage.php Settings API page under Settings → Age Verification.
src/Frontend/Gate.php         Hooked on template_redirect; decides whether to render.
src/Frontend/Renderer.php     Streams the standalone overlay HTML document.
src/Frontend/Assets.php       Versioned asset URL helper.
src/Verification/AjaxHandler.php   wp_ajax_hoay_verify endpoint.
src/Verification/AgeCalculator.php Pure DOB → age, timezone-safe.
src/Verification/CookieManager.php HMAC-signed cookie read/write/clear.
src/Support/BotDetector.php   Pure UA matcher with built-in token list.
src/Support/Sanitizer.php     Type-aware input sanitisation helpers.
src/Support/Template.php      Template loader with theme-override support.
templates/modal.php           Verification overlay markup.
templates/admin/settings-page.php Admin settings UI.
```

## Request flow

1. **`plugins_loaded`** — `Plugin::boot()` registers admin and frontend components. Admin requests get the settings page; everything else gets the gate.
2. **`template_redirect`** — `Gate::maybe_render()` runs. It calls `should_gate()` which checks: enabled, not an exempt request (admin, REST, AJAX, cron, XML-RPC, login, robots, feeds, configured paths, bot UAs), and no valid signed cookie. If all true, `Renderer::render()` streams the overlay and `exit;` — the page body never reaches the browser.
3. **`wp_ajax_hoay_verify`** — `AjaxHandler::handle()` validates a nonce, runs the DOB or confirm payload through `AgeCalculator` / sanity checks, and on success calls `CookieManager::set_verified()`. On failure no cookie is written.

## Cookie format

```
v1|<expiry>|<hmac>
```

- `v1` — payload version. Future formats will bump this; old versions fail verification.
- `<expiry>` — unix timestamp. The cookie's HTTP expiry matches this value.
- `<hmac>` — `hash_hmac('sha256', "v1.<expiry>", wp_salt('auth') . '|hoay')`.

A visitor who tries to extend `<expiry>` with a copied HMAC fails verification because the HMAC includes the expiry as input.

## Adding a setting

1. Add a key + default to `Options::defaults()`.
2. Add a sanitisation branch to `SettingsPage::sanitize()` using a `Sanitizer::*` helper.
3. Add a row to `templates/admin/settings-page.php`.
4. Read the value from your code via `Options::get( 'your_key' )`.
5. If it's a theming setting that should be exposed as a CSS variable, add it to `Renderer::css_variables()` and the relevant rule in `assets/css/frontend.css`.
6. Add a test to `tests/unit/Frontend/RendererCssVariablesTest.php` for theming settings, or `tests/unit/Support/SanitizerTest.php` for input validation.

## Running tests

```sh
docker run --rm -v "$PWD":/app -w /app composer:2 install
docker run --rm -v "$PWD":/app -w /app composer:2 run test:unit
```

The integration suite (`tests/integration/`) skips when `WP_TESTS_DIR` is unset; to run it, install `wp-phpunit/wp-phpunit` and bootstrap accordingly.

## Coding standards

```sh
docker run --rm -v "$PWD":/app -w /app composer:2 run lint        # check
docker run --rm -v "$PWD":/app -w /app composer:2 run lint:fix    # auto-fix
```

Standards: WordPress-Extra + WordPress-Docs + PHPCompatibilityWP, scoped to runtime files.
