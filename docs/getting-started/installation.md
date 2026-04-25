---
title: Installation
parent: Getting started
nav_order: 1
permalink: /getting-started/installation/
---

# Installation

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher

## From a release zip

1. Download the latest release zip from the [Releases page](https://github.com/farzad1120/how-old-are-you/releases).
2. In WP admin, go to **Plugins → Add New → Upload Plugin**, choose the zip, click **Install Now**.
3. Click **Activate**.
4. Visit **Settings → Age Verification** to configure.

## From source

```sh
cd wp-content/plugins/
git clone https://github.com/farzad1120/how-old-are-you.git
```

The plugin runs without `vendor/` because it ships a PSR-4 fallback autoloader. `composer install` is only needed for development tooling (PHPCS, PHPUnit).

## Composer-managed sites (Bedrock, Roots, …)

```json
{
  "require": {
    "farzad1120/how-old-are-you": "^1.0"
  }
}
```

The plugin's `composer.json` declares `type: wordpress-plugin`, so it lands in `wp-content/plugins/how-old-are-you/` automatically.

## Activation behavior

On first activation, default settings are seeded into a single option (`hoay_settings`). Subsequent activations leave existing settings untouched.

## Uninstall

Deleting the plugin via the WP admin (not just deactivation) runs `uninstall.php`, which removes:

- The `hoay_settings` option.
- The `hoay_version` option.

On multisite, the same options are removed from every site in the network.
