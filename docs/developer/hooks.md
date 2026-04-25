---
title: Hooks
parent: For developers
nav_order: 2
permalink: /developer/hooks/
---

# Hooks reference

Every filter the plugin provides, with a short example.

## `hoay_should_gate` *(bool)*

Final say on whether the gate renders for the current request. Receives `true` after `Gate::should_gate()` has confirmed the request would otherwise be gated.

```php
add_filter( 'hoay_should_gate', function ( $should ) {
    if ( current_user_can( 'edit_posts' ) ) {
        return false; // Editors and above bypass the gate.
    }
    return $should;
} );
```

## `hoay_excluded_paths` *(string[])*

Modify the list of path prefixes that bypass the gate. Receives the admin-configured list.

```php
add_filter( 'hoay_excluded_paths', function ( array $paths ) {
    $paths[] = '/blog/teaser';
    return $paths;
} );
```

## `hoay_verification_result` *(array)*

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

## `hoay_cookie_args` *(array)*

Adjust cookie options before `setcookie()` is called.

```php
add_filter( 'hoay_cookie_args', function ( array $args, string $cookie_name ) {
    $args['domain'] = '.example.com'; // Share verification across subdomains.
    return $args;
}, 10, 2 );
```

## `hoay_bot_tokens` *(string[])*

Modify the effective list of UA tokens that bypass the gate. Receives the configured list (or `BotDetector::DEFAULT_TOKENS` when the admin field is empty).

```php
add_filter( 'hoay_bot_tokens', function ( array $tokens, string $user_agent ) {
    $tokens[] = 'MyCustomBot';
    return $tokens;
}, 10, 2 );
```

## `hoay_is_search_bot` *(bool)*

Final override of the bot decision after `BotDetector::is_bot()` has run.

```php
add_filter( 'hoay_is_search_bot', function ( bool $is_bot, string $user_agent ) {
    if ( str_contains( $user_agent, 'corp-monitor' ) ) {
        return true; // Always treat the internal monitor as a bot.
    }
    return $is_bot;
}, 10, 2 );
```
