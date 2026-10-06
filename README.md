# Sikora Google reCAPTCHA on WordPress Login

Adds Google's reCAPTCHA functionality to the WordPress login page.

## Requirements

- WordPress 5.8+
- PHP 7.4+
- Google reCAPTCHA v2 **ID** and **Secret Key**

## Installation

1. Copy this folder into `wp-content/plugins/`, or install the zip from `./build.sh`.
2. Activate **Sikora Google reCAPTCHA on WordPress Login** in **Plugins**.
3. Go to **Settings → Sikora Google reCAPTCHA on WordPress Login**.
4. Under **Google reCAPTCHA v2**, paste your **ID** and **Secret Key**. Generate the values using [https://www.google.com/recaptcha/admin](https://www.google.com/recaptcha/admin).
5. Save changes.

The instructions on the following page will help you generate the values you need for this plugin: [https://www.google.com/recaptcha/admin](https://www.google.com/recaptcha/admin)

## Behavior

- Values for the settings fields should be created with Google's reCAPTCHA admin console.
- The reCAPTCHA element will not appear on the WordPress login page if any value is missing.
- When both values are saved, the reCAPTCHA widget appears on the login form.
- Login is blocked until Google verifies a successful reCAPTCHA response.

## Build

Run the executable `build.sh` file to create a zip you can install in WordPress.

## Uninstall

When the plugin is deleted, everything it stored is removed—including its settings—so nothing from the plugin remains on the site (including on every site in a multisite network).

## Project Structure

```
sikora-google-recaptcha-on-wordpress-login/
├── sikora-google-recaptcha-on-wordpress-login.php  # plugin bootstrap
├── includes/
│   ├── class-sgrwl-settings.php                    # admin settings page
│   └── class-sgrwl-login.php                       # login widget + verification
├── build.sh                                        # builds installable plugin zip
├── uninstall.php                                   # removes options on plugin delete
├── readme.txt                                      # WordPress.org plugin readme
└── README.md
```
