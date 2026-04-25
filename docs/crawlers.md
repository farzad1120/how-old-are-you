---
title: Crawlers
nav_order: 5
permalink: /crawlers/
---

# Crawlers

The age gate replaces the page body for unverified visitors. Without bot bypass, that means search engines and social-media link unfurlers would see the verification overlay instead of your real content. This page covers how the bypass works and how to tune it.

## How the bypass works

On every front-end request, the plugin reads the `User-Agent` header and matches it (case-insensitive substring) against a configured list of tokens. If any token matches, the gate is skipped entirely and the request continues to the normal WordPress rendering.

The match is intentionally simple — it doesn't try to verify the bot is "really" Googlebot via reverse DNS lookup, because that would add latency to every request. The tradeoff: a determined visitor can spoof their `User-Agent` to bypass the gate. This is consistent with how every age-gate plugin works, and what bots see is the same content a verified human sees, so it's not deceptive.

## Default token list

The built-in list (used when **Crawler user agents** is left empty) covers:

| Group | Tokens |
|---|---|
| Search engines | `Googlebot`, `Bingbot`, `Slurp`, `DuckDuckBot`, `Baiduspider`, `YandexBot`, `Sogou`, `Exabot`, `Applebot`, `PetalBot`, `SeznamBot`, `ia_archiver` |
| Social previews | `facebookexternalhit`, `Facebot`, `Twitterbot`, `LinkedInBot`, `Pinterestbot`, `TelegramBot`, `Discordbot`, `Slackbot`, `WhatsApp`, `SkypeUriPreview`, `redditbot` |
| Archivers | `archive.org_bot`, `Wayback` |
| Ads / structured data | `Mediapartners-Google`, `AdsBot-Google`, `Google-Site-Verification` |

## Customizing the list

Drop your own list into **Settings → Age Verification → Crawlers → Crawler user agents**. One token per line. If the field is non-empty, it **replaces** the built-in list; if you want to extend rather than replace, copy the built-in tokens you want to keep and add yours below.

Example: a strict list that only allows the major search engines and your custom monitor:

```
Googlebot
Bingbot
DuckDuckBot
my-internal-monitor
```

## Disabling bypass entirely

Uncheck **Bypass for crawlers**. The gate will then render for every front-end request, including bots, and your site will not be indexed. Consider this only if your compliance regime explicitly requires no UA-based bypass.

## Custom logic via a filter

For per-request logic (e.g. "treat any UA containing the string `monitor` as a bot"), use the `hoay_is_search_bot` filter:

```php
add_filter( 'hoay_is_search_bot', function ( bool $is_bot, string $user_agent ) {
    if ( str_contains( $user_agent, 'monitor' ) ) {
        return true;
    }
    return $is_bot;
}, 10, 2 );
```

You can also modify the effective token list per request:

```php
add_filter( 'hoay_bot_tokens', function ( array $tokens ) {
    $tokens[] = 'MyCustomBot';
    return $tokens;
} );
```

## What bots see

Bots that bypass the gate see the same HTML a verified human sees: the normal WordPress theme rendering of the requested URL. There's no special "bot version" — that would be cloaking, which Google explicitly disallows.

Bots that don't match (i.e. unknown UAs, or all UAs when bypass is disabled) see the verification overlay, which carries `<meta name="robots" content="noindex,nofollow">` so it doesn't get indexed.

## Always-exempt requests

Regardless of the bypass setting, these requests never see the gate:

- `wp-admin/*`, `wp-login.php`, `wp-register.php`
- REST (`/wp-json/...`)
- AJAX (`admin-ajax.php`)
- Cron (`wp-cron.php`)
- XML-RPC (`xmlrpc.php`)
- robots.txt, feeds (RSS, Atom), trackbacks
- Non-GET requests

So your sitemap, RSS feed, and robots.txt always work for crawlers, even with bypass disabled.
