---
title: Home
layout: home
nav_order: 1
description: Block under-age visitors from your WordPress site with a customizable age-verification gate.
permalink: /
---

# How Old Are You
{: .fs-9 }

A focused, production-ready WordPress age-verification plugin. Blocks the public frontend behind a customizable verification screen and remembers verified visitors via a signed cookie.
{: .fs-6 .fw-300 }

[Get started]({{ "/getting-started/" | relative_url }}){: .btn .btn-primary .fs-5 .mb-4 .mb-md-0 .mr-2 }
[View on GitHub](https://github.com/farzad1120/how-old-are-you){: .btn .fs-5 .mb-4 .mb-md-0 }

---

## What it does

- **Two verification modes** — visitors enter their date of birth, or click an "I am over X" / "I am under X" pair of buttons. Site admin chooses.
- **Site-locale-aware** — the date input follows the WordPress locale (format hint, language attribute, optional dropdown selectors with localized month names).
- **Signed cookie** — once verified, a visitor isn't re-prompted for the configured lifetime. The cookie is HMAC-signed so it can't be forged in browser dev tools.
- **No bypass on failure** — under-age visitors get a rejection message and no cookie. Refreshing re-prompts.
- **Standalone gate document** — the page body is never served to unverified visitors, even via "view source".
- **SEO-friendly** — search engines and social-media unfurlers see the real page, so your site stays indexed and link previews work.
- **Fully themable** — every color, size, radius, font, and string is configurable from the admin UI, with a Custom CSS field on top.

## Quick start

```sh
# Drop the plugin into your WordPress install.
cd wp-content/plugins/
git clone https://github.com/farzad1120/how-old-are-you.git
```

Then activate **How Old Are You** under **Plugins → Installed Plugins**, and visit **Settings → Age Verification** to configure.

See [Installation]({{ "/getting-started/installation/" | relative_url }}) for release-zip and Composer flows.

## What you get out of the box

| Feature | Default |
|---|---|
| Minimum age | 18 |
| Verification mode | Date of birth picker |
| Cookie lifetime | 30 days |
| Crawler bypass | Enabled (Googlebot, Bingbot, Twitterbot, facebookexternalhit, etc.) |
| Theme | Dark backdrop (#0b0b0b), white panel, gold accent |

Everything is changeable.

## Going further

- [Configure every setting]({{ "/configuration/" | relative_url }})
- [Customize the look]({{ "/customization/" | relative_url }})
- [Crawler bypass details]({{ "/crawlers/" | relative_url }})
- [Troubleshooting]({{ "/troubleshooting/" | relative_url }})
- [For developers]({{ "/developer/" | relative_url }}) (filters, template overrides, security model)

## License

[GPL-2.0-or-later](https://www.gnu.org/licenses/old-licenses/gpl-2.0.html). Free as in freedom.
