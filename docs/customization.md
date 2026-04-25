---
title: Customization
nav_order: 4
permalink: /customization/
---

# Customization

The Appearance section in admin is split into six subsections, each tuning a different group of CSS custom properties.

## Logo

| Field | Type | Default | Notes |
|---|---|---|---|
| Logo | media library image | none | Optional, displayed centered above the heading. |
| Logo max width (px) | int 40–400 | `160` | Caps the rendered logo width. |

## Background

| Field | Type | Default | Notes |
|---|---|---|---|
| Background color | hex | `#0b0b0b` | Page background behind the panel. |
| Background image | media library image | none | Optional image laid over the background color. |
| Background image size | enum | `cover` | `cover`, `contain`, or `auto` (CSS `background-size`). |
| Overlay opacity | float 0–1 | `0.92` | Darkens whatever is behind the panel. |
| Backdrop blur (px) | int 0–32 | `0` | Frosted-glass effect via `backdrop-filter: blur()`. |

## Panel

| Field | Type | Default | Notes |
|---|---|---|---|
| Panel color | hex | `#ffffff` | Card background. |
| Panel width (px) | int 320–720 | `440` | Maximum width of the card. |
| Panel padding (px) | int 16–64 | `36` | Inner spacing. |
| Panel border radius (px) | int 0–32 | `12` | |

## Typography

| Field | Type | Default | Notes |
|---|---|---|---|
| Font family | text | empty | CSS font stack. Empty falls back to a system stack. Quotes, commas, hyphens, and spaces only. |
| Body font size (px) | int 12–24 | `16` | |
| Heading font size (px) | int 16–48 | `22` | |
| Text color | hex | `#111111` | |
| Text alignment | enum | `center` | `left`, `center`, or `right`. |

## Controls

| Field | Type | Default | Notes |
|---|---|---|---|
| Accent color | hex | `#c7a008` | Primary button background and focus ring. |
| Button border radius (px) | int 0–32 | `8` | |
| Input border radius (px) | int 0–32 | `8` | |

## Custom CSS

A textarea scoped to the verification overlay. Tags and obvious script sequences are stripped on save (`<`, `>`, `javascript:`, `expression(`, `behavior:`, `behaviour:`, `@import`).

Every theming setting above is exposed as a CSS custom property, so Custom CSS can compose with them. Available variables:

| Variable | What it controls |
|---|---|
| `--hoay-bg` | Background color |
| `--hoay-bg-image` | Background image (`url("...")` or `none`) |
| `--hoay-bg-size` | `cover`, `contain`, or `auto` |
| `--hoay-opacity` | Backdrop darken opacity (0–1) |
| `--hoay-blur` | Backdrop blur in px |
| `--hoay-panel` | Panel background color |
| `--hoay-panel-width` | Panel width in px |
| `--hoay-panel-padding` | Panel padding in px |
| `--hoay-panel-radius` | Panel border radius in px |
| `--hoay-text` | Text color |
| `--hoay-text-align` | `left` / `center` / `right` |
| `--hoay-accent` | Primary accent color |
| `--hoay-font` | Font stack |
| `--hoay-font-size` | Body font size in px |
| `--hoay-heading-size` | Heading font size in px |
| `--hoay-button-radius` | Button border radius in px |
| `--hoay-input-radius` | Input border radius in px |
| `--hoay-logo-max` | Logo max width in px |

## Sample CSS to test it

Paste this into the Custom CSS field. It uses every variable, adds a glassmorphism panel, an entrance animation, a gradient heading, and a floating logo.

```css
/* Soft gradient background using the configured colors */
.hoay-overlay {
  background: linear-gradient(135deg, var(--hoay-bg) 0%, var(--hoay-accent) 200%);
}

/* Glassmorphism panel + entrance animation */
.hoay-panel {
  background: color-mix(in srgb, var(--hoay-panel) 88%, transparent);
  -webkit-backdrop-filter: blur(14px) saturate(140%);
  backdrop-filter: blur(14px) saturate(140%);
  border: 1px solid color-mix(in srgb, var(--hoay-text) 12%, transparent);
  box-shadow:
    0 24px 60px rgba(0, 0, 0, 0.35),
    0 1px 0 rgba(255, 255, 255, 0.4) inset;
  animation: hoay-rise 480ms cubic-bezier(0.2, 0.7, 0.2, 1);
}

@keyframes hoay-rise {
  from { opacity: 0; transform: translateY(16px) scale(0.98); }
  to   { opacity: 1; transform: translateY(0)    scale(1); }
}

/* Heading: tighter tracking + gradient text */
.hoay-heading {
  letter-spacing: -0.01em;
  background: linear-gradient(180deg, var(--hoay-text), var(--hoay-accent));
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

/* Floating logo */
.hoay-logo {
  animation: hoay-float 4s ease-in-out infinite;
}
@keyframes hoay-float {
  0%, 100% { transform: translateY(0); }
  50%      { transform: translateY(-3px); }
}

/* Primary button: gradient + hover lift */
.hoay-button--primary {
  background: linear-gradient(135deg, var(--hoay-accent), color-mix(in srgb, var(--hoay-accent) 70%, black));
  box-shadow: 0 6px 18px color-mix(in srgb, var(--hoay-accent) 40%, transparent);
}
.hoay-button--primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 10px 24px color-mix(in srgb, var(--hoay-accent) 55%, transparent);
}

/* Soften input borders, lift on focus */
.hoay-input,
.hoay-select {
  border-color: color-mix(in srgb, var(--hoay-text) 14%, transparent);
}
.hoay-input:focus,
.hoay-select:focus {
  transform: translateY(-1px);
}
```

## Theme override

For full control of the modal markup, copy `templates/modal.php` from the plugin into your active theme at `<theme>/how-old-are-you/modal.php`. The plugin will prefer the theme copy.

The variables passed in are documented at the top of `templates/modal.php`:

```
@var array  $options    Current settings.
@var int    $min_age    Minimum age requirement.
@var string $mode       'dob' or 'confirm'.
@var string $logo_url   Logo URL or empty.
@var string $nonce      hoay_verify nonce.
@var string $ajax_url   admin-ajax URL.
@var string $format_hint Localized DOB format hint.
@var array  $months     Site-localized month names.
…
```
