---
title: First-time setup
parent: Getting started
nav_order: 2
permalink: /getting-started/setup/
---

# First-time setup

After activating the plugin, the gate is **on** and uses the defaults below. Most sites only need to change a handful of settings before going live.

## 1. Set the minimum age

**Settings → Age Verification → Behavior → Minimum age**

The default is `18`. Change it to whatever your jurisdiction requires (typically 18 or 21).

## 2. Pick a verification mode

**Settings → Age Verification → Behavior → Verification mode**

| Mode | When to choose it |
|---|---|
| **Date of birth** *(default)* | Your jurisdiction expects a real age check (alcohol, cannabis, vape, firearms). |
| **Confirmation** | A simple "I am over X" / "I am under X" button pair. Lower friction, weaker as a compliance control. |

## 3. Pick the DOB input style (DOB mode only)

**Settings → Age Verification → Behavior → DOB input style**

| Style | What it looks like |
|---|---|
| **Native HTML5** *(default)* | Browser's built-in date picker. Format follows the document's language attribute. |
| **Site-localized dropdowns** | Three `<select>` fields (day / month / year). Month names follow the WordPress locale via `date_i18n()` on every browser. |

## 4. Customize the messages

**Settings → Age Verification → Messages**

All user-facing text — heading, body, button labels, rejection text — is editable. You can use the placeholder `{age}` anywhere; it's replaced with the configured Minimum age at render time.

## 5. Make it look like your brand

**Settings → Age Verification → Appearance**

Pick a logo from the media library, set the background and panel colors, and pick a font. See [Customization]({{ "/customization/" | relative_url }}) for the full reference and a sample CSS snippet to test the Custom CSS field.

## Test it

Open your site in an **incognito window** (so you don't carry the wp-admin login cookie) and visit the homepage:

1. The verification overlay should appear instead of your homepage.
2. Try entering a too-young DOB → rejection message, no cookie.
3. Try entering a valid DOB → modal closes, cookie is set, refresh keeps you in.
4. Open dev tools → Application → Cookies. The cookie value is `v1|<expiry>|<hmac>` — try editing the HMAC; on refresh the gate should re-render (proving the cookie can't be forged).

If something doesn't work as expected, check [Troubleshooting]({{ "/troubleshooting/" | relative_url }}).
