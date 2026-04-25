---
title: Troubleshooting
nav_order: 6
permalink: /troubleshooting/
---

# Troubleshooting

## I changed a setting but nothing changed on the front end

1. Visit the site in an **incognito** window to bypass any verification cookie left from previous sessions.
2. Force-reload (Ctrl/Cmd-Shift-R) so cached CSS doesn't mask the change.
3. If you set a background image and don't see it, view-source on the gate and look for the `<style id="hoay-vars">` block — every theming setting should appear as a `--hoay-*` CSS variable.

## The gate isn't appearing at all

- Check **Enable gate** is on under Behavior.
- Check the cookie hasn't already verified you. Open dev tools → Application → Cookies and delete the cookie named `hoay_verified` (or whatever you renamed it to).
- Check your URL doesn't match an excluded path prefix.
- If you're a logged-in user with `manage_options`, you're not auto-bypassed by default — but a custom theme or another plugin might be filtering `hoay_should_gate` to false.

## I see the gate when sharing a link on Facebook/Twitter/LinkedIn

The bot bypass should handle this. Check **Bypass for crawlers** is on under Crawlers. If it's already on:

- Some networks fetch from a service whose UA isn't on the default list. Add the missing token (one per line) to **Crawler user agents**.
- Verify by faking the UA in curl:
  ```sh
  curl -A "facebookexternalhit/1.1" https://your-site.example/some-page
  ```
  You should see your real page HTML, not the gate.

## My custom CSS doesn't seem to apply

- The Custom CSS field strips `<`, `>`, and dangerous keywords (`javascript:`, `expression(`, `behavior:`, `behaviour:`, `@import`) on save. If your snippet relies on any of those, it won't work — that's by design.
- Check the rendered overlay's `<style id="hoay-vars">` block in view-source. Your CSS should be there, after the variables block.
- Selectors that target page elements outside the overlay (e.g. `body`, `html`) should still work because the gate is a complete HTML document.

## The DOB picker shows MM/DD/YYYY but I want DD/MM/YYYY

Two options:

1. **Site-localized dropdowns mode** under Behavior → DOB input style. This always uses the WordPress locale's month names and gives you a deterministic order.
2. **Native HTML5 picker** — the format follows the browser's locale, not the site's. Set the site language to a locale that uses DD/MM/YYYY (e.g. `en_GB`, `nl_NL`, `de_DE`) under **Settings → General → Site Language**. The plugin automatically passes the language attribute to the input.

## A visitor reports they can't enter even though they're old enough

- They might be in a different timezone where the date is "earlier" than yours. Age is computed in the **site's** timezone (set under Settings → General → Timezone). Make sure that's set correctly for your business.
- The cookie might be stuck. Have them clear cookies for your domain.

## Tampered cookies aren't working / I want to test cookie integrity

The cookie value is `v1|<expiry>|<hmac>`. Try editing the HMAC portion in dev tools. On reload, the gate should re-render. If it doesn't:

- Make sure the cookie domain in dev tools is exactly your site's domain (not a parent domain).
- Try changing the expiry instead — that's also signed, so any change should invalidate.

## Multisite: settings reset on every site

Each site in a multisite network has its own `hoay_settings`. Defaults are seeded on activation per site. If you want a single shared configuration, use a `hoay_settings` filter or copy the option via WP-CLI:

```sh
wp option get hoay_settings --format=json --url=https://primary.example > settings.json
wp option update hoay_settings "$(cat settings.json)" --format=json --url=https://secondary.example
```

## My SEO plugin's meta tags vanish on the gate

That's expected. The gate is a standalone document — your theme's `wp_head` (and any plugin that hooks into it) doesn't run. The gate emits its own minimal head: charset, viewport, `<meta name="robots" content="noindex,nofollow">`, title, and stylesheet. The real page (which bots and verified humans see) carries your theme's full head.

## Where can I report a bug?

[GitHub Issues](https://github.com/farzad1120/how-old-are-you/issues). For security issues, see the [Security]({{ "/developer/security/" | relative_url }}) page.
