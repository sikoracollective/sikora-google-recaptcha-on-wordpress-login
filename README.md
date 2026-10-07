# Sikora Login reCAPTCHA

Adds Google reCAPTCHA v2 to the WordPress login page.

This plugin is licensed under [GPLv2 or later](https://www.gnu.org/licenses/gpl-2.0.html). See `license.txt`.

reCAPTCHA® is a trademark of Google LLC. This plugin is not affiliated with or endorsed by Google.

## Requirements

- WordPress 5.8+
- PHP 7.4+
- Google reCAPTCHA v2 **site key (ID)** and **Secret Key**

## Third-party service

The plugin is an interface to [Google reCAPTCHA](https://developers.google.com/recaptcha):

- [Google Terms of Service](https://policies.google.com/terms)
- [Google Privacy Policy](https://policies.google.com/privacy)
- Credential console: [https://www.google.com/recaptcha/admin](https://www.google.com/recaptcha/admin)

When keys are saved, the login page loads `https://www.google.com/recaptcha/api.js`, and the server verifies tokens at `https://www.google.com/recaptcha/api/siteverify` (may include the visitor IP). No data is sent to Sikora Collective.

## Installation

1. Copy this folder into `wp-content/plugins/`, or install the zip from `./build.sh`.
2. Activate **Sikora Login reCAPTCHA** in **Plugins**.
3. Go to **Settings → Sikora Login reCAPTCHA**.
4. Under **Google reCAPTCHA v2**, paste your **ID** and **Secret Key**.
5. Save changes.

## Behavior

- Values for the settings fields should be created with Google's reCAPTCHA admin console.
- The reCAPTCHA element will not appear on the WordPress login page if any value is missing.
- When both values are saved, the reCAPTCHA widget appears on the login form.
- Login is blocked unless Google returns a successful reCAPTCHA response for an allowed hostname.

## Build

Source PHP in this repository is the deployed source (human-readable; no build step for runtime code).

Run `./build.sh` to create an installable zip for WordPress. The script stages the plugin files listed in `INCLUDE_PATHS` and writes `sikora-login-recaptcha.zip` in the project root.

## Uninstall

When the plugin is deleted, everything it stored is removed—including its settings—so nothing from the plugin remains on the site (including on every site in a multisite network).

## Project Structure

```
sikora-login-recaptcha/
├── sikora-login-recaptcha.php  # plugin bootstrap
├── includes/
│   ├── class-slr-settings.php  # admin settings page
│   └── class-slr-login.php     # login widget + verification
├── build.sh                    # builds installable plugin zip
├── uninstall.php               # removes options on plugin delete
├── license.txt                 # GPLv2 or later
├── readme.txt                  # WordPress.org plugin readme
└── README.md
```
