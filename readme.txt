=== Sikora Google reCAPTCHA on WordPress Login ===
Contributors: sikoracollective
Tags: recaptcha, login, security, google recaptcha, spam
Requires at least: 5.8
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds Google's reCAPTCHA functionality to the WordPress login page.

== Description ==

This plugin adds Google's reCAPTCHA functionality to the WordPress login page.

Features:

* Adds the reCAPTCHA checkbox to the login page
* Verifies the response with Google before credentials are checked
* Settings page (**Settings → Sikora Google reCAPTCHA on WordPress Login**) for your ID and Secret Key
* Leaves login unchanged until both values are configured (avoids accidental lockouts)
* On delete, removes everything it stored—including settings—so nothing from the plugin remains

The instructions on the following page will help you generate the values you need for this plugin: https://www.google.com/recaptcha/admin

On the settings page under **Google reCAPTCHA v2**:

* Values for the fields below should be created with Google's reCAPTCHA admin console.
* The reCAPTCHA element will not appear on the WordPress login page if any value is missing.

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install the zip through **Plugins → Add New**.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Settings → Sikora Google reCAPTCHA on WordPress Login**.
4. Enter your Google reCAPTCHA v2 ID and Secret Key. Generate the values using https://www.google.com/recaptcha/admin.
5. Save changes and open the login page to confirm the widget appears.

== Frequently Asked Questions ==

= Which reCAPTCHA version is supported? =

Google reCAPTCHA v2 ("I'm not a robot" Checkbox).

= What happens if I activate the plugin without values? =

Nothing changes on the login page until both the ID and Secret Key are saved.

= Where do I get my ID and Secret Key? =

From Google's reCAPTCHA admin console: https://www.google.com/recaptcha/admin

= What happens when I delete the plugin? =

Everything the plugin stored is removed—including its settings—so nothing from the plugin remains on the site.

== Changelog ==

= 1.1.0 =
* Hardened reCAPTCHA verification with hostname checks.
* Stopped autoloading settings that include the secret key.
* Improved login-page layout and settings UI.
* Added debug logging for Google request failures and hostname mismatches.

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.1.0 =
Hardening and reliability updates for login verification and settings storage.

= 1.0.0 =
Initial release.
