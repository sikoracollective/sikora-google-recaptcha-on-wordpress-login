# Sikora Google reCAPTCHA on WordPress Login

WordPress plugin by Sikora Collective that adds Google reCAPTCHA v2 ("I'm not a robot") to the login page (`wp-login.php`).

## Requirements

- WordPress 5.8+
- PHP 7.4+
- Google reCAPTCHA v2 **ID** and **Secret Key**

## Installation

1. Copy this folder into `wp-content/plugins/`, or install the zip from `./build.sh`.
2. Activate **Sikora Google reCAPTCHA on WordPress Login** in **Plugins**.
3. Go to **Settings → Sikora Google reCAPTCHA on WordPress Login**.
4. Under **Google reCAPTCHA v2**, paste your **ID** and **Secret Key**.
5. Save changes.

Create values at: [https://www.google.com/recaptcha/admin](https://www.google.com/recaptcha/admin)  
Choose **reCAPTCHA v2 → "I'm not a robot" Checkbox** and add your site domain.

## Behavior

- Values for the settings fields should be created with Google's reCAPTCHA admin console.
- The reCAPTCHA element will not appear on the WordPress login page if both values are missing.
- When both values are saved, the reCAPTCHA widget appears on the login form.
- Login is blocked until Google verifies a successful reCAPTCHA response.

## Build

```bash
./build.sh
```

Creates `sikora-google-recaptcha-on-wordpress-login.zip` in the project root, lists each included file, prints how many files were added, and removes temporary build files. `README.md` and `build.sh` are not packaged in the zip.

## Uninstall

Deleting the plugin removes the saved `sgrwl_settings` option (including on every site in a multisite network).

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
