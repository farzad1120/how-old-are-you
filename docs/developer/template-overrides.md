---
title: Template overrides
parent: For developers
nav_order: 3
permalink: /developer/template-overrides/
---

# Template overrides

For full control of the modal markup, copy `templates/modal.php` from the plugin into your active theme:

```
<active-theme>/how-old-are-you/modal.php
```

`HOAY\Support\Template::locate()` prefers the theme copy over the plugin's `templates/modal.php`. The plugin keeps streaming a complete standalone document — your override only changes what's inside it.

## Variables passed in

```
@var array<string,mixed> $options    Current settings array.
@var int                 $min_age    Minimum age (already an int).
@var string              $mode       'dob' or 'confirm'.
@var string              $logo_url   Logo URL or empty string.
@var string              $nonce      hoay_verify nonce — keep it on the form!
@var string              $ajax_url   admin-ajax URL.
@var string              $max_dob    Today's Y-m-d (DOB upper bound).
@var string              $min_dob    120 years ago Y-m-d (DOB lower bound).
@var string              $assets     URL to /assets/.
@var string              $css_ver    Asset version (HOAY_VERSION).
@var string              $site_url   Site home URL.
@var string              $site_name  Site title.
@var string              $lang       Full language code (e.g. en-US).
@var string              $lang_short Short locale (en, nl, de).
@var string              $format_hint Localized DOB format hint (e.g. "DD-MM-YYYY").
@var array<int,string>   $months     Site-localized month names indexed 1–12.
@var int                 $year_max   Latest selectable year.
@var int                 $year_min   Earliest selectable year.
```

## What you must keep

For the AJAX flow to work the form needs:

- `data-mode="<dob|confirm>"`
- `data-min-age="<n>"`
- `data-ajax="<admin-ajax url>"`
- `data-nonce="<nonce>"`
- A submit button that triggers the form's submit event.
- For DOB mode: an input with id `hoay-dob` (native style) **or** three selects with ids `hoay-dob-day` / `hoay-dob-month` / `hoay-dob-year` (selects style).
- For Confirm mode: two submit buttons each with `data-confirm="yes"` or `data-confirm="no"`.

The default `assets/js/frontend.js` reads these and posts them. Keep it loaded with `<script src="<?php echo esc_url( $assets . 'js/frontend.js?ver=' . rawurlencode( $css_ver ) ); ?>"></script>` near the end of `<body>`.

## Beyond layout

If you want fundamentally different markup or behavior, you might be better off forking the plugin. The template override is intended for visual changes that the CSS variables and Custom CSS field can't express.
