# Developer reference

## Architecture overview

```
how-old-are-you.php           Plugin bootstrap (header, constants, autoload, plugins_loaded)
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
src/Support/Sanitizer.php     Type-aware input sanitisation helpers.
src/Support/Template.php      Template loader with theme-override support.
templates/modal.php           Verification overlay markup.
templates/admin/settings-page.php Admin settings UI.
```

## Filters

### `hoay_should_gate` *(bool)*

Final say on whether the gate renders for the current request. Receives `true` after Gate::should_gate() has confirmed the request would otherwise be gated.

```php
add_filter( 'hoay_should_gate', function ( $should ) {
    if ( current_user_can( 'edit_posts' ) ) {
        return false; // Editors and above bypass the gate.
    }
    return $should;
} );
```

### `hoay_excluded_paths` *(string[])*

Modify the list of path prefixes that bypass the gate. Receives the admin-configured list.

```php
add_filter( 'hoay_excluded_paths', function ( array $paths ) {
    $paths[] = '/blog/teaser';
    return $paths;
} );
```

### `hoay_verification_result` *(array)*

Mutate the verification result before it's encoded as JSON.

```php
add_filter( 'hoay_verification_result', function ( array $result, string $mode ) {
    if ( ! empty( $result['passed'] ) ) {
        do_action( 'my_app/age_verified', $mode );
    }
    return $result;
}, 10, 2 );
```

The result shape is:

```php
[
    'passed'  => bool,
    'reason'  => 'underage' | 'invalid' | 'disabled', // when passed === false
    'message' => string,
]
```

### `hoay_bot_tokens` *(string[])*

Modify the effective list of UA tokens that bypass the gate. Receives the configured list (or `BotDetector::DEFAULT_TOKENS` when the admin field is empty).

```php
add_filter( 'hoay_bot_tokens', function ( array $tokens, string $user_agent ) {
    $tokens[] = 'MyCustomBot';
    return $tokens;
}, 10, 2 );
```

### `hoay_is_search_bot` *(bool)*

Final override of the bot decision after `BotDetector::is_bot()` has run.

```php
add_filter( 'hoay_is_search_bot', function ( bool $is_bot, string $user_agent ) {
    if ( str_contains( $user_agent, 'corp-monitor' ) ) {
        return true; // Always treat the internal monitor as a bot.
    }
    return $is_bot;
}, 10, 2 );
```

### `hoay_cookie_args` *(array)*

Adjust cookie options before `setcookie()` is called.

```php
add_filter( 'hoay_cookie_args', function ( array $args, string $cookie_name ) {
    $args['domain'] = '.example.com'; // Share verification across subdomains.
    return $args;
}, 10, 2 );
```

## Template overrides

Place a copy of the file in your theme:

```
<active-theme>/how-old-are-you/modal.php
```

`HOAY\Support\Template::locate()` prefers the theme copy over the plugin's `templates/modal.php`. The variables passed in are documented in the `@var` annotations at the top of `templates/modal.php`.

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
4. Read it from your code via `Options::get( 'your_key' )`.

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
