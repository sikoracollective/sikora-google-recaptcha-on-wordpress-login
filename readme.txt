=== Sikora Google reCAPTCHA on WordPress Login ===
Contributors: sikoracollective
Tags: recaptcha, login, security, google recaptcha, spam
Requires at least: 5.8
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Adds Google reCAPTCHA v2 ("I'm not a robot") to the WordPress login page to help block automated login attempts.

== Description ==

This plugin by Sikora Collective protects the WordPress login form (`wp-login.php`) with Google reCAPTCHA v2.

Features:

* Adds the reCAPTCHA checkbox to the login page
* Verifies the response with Google before credentials are checked
* Settings page (**Settings → Sikora Google reCAPTCHA on WordPress Login**) for your ID and Secret Key
* Leaves login unchanged until both values are configured (avoids accidental lockouts)
* Removes saved settings when the plugin is deleted

Create values at: https://www.google.com/recaptcha/admin

Choose **reCAPTCHA v2 → "I'm not a robot" Checkbox** and add your site domain.

On the settings page under **Google reCAPTCHA v2**:

* Values for the fields below should be created with Google's reCAPTCHA admin console.
* The reCAPTCHA element will not appear on the WordPress login page if both values are missing.

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory, or install the zip through **Plugins → Add New**.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Settings → Sikora Google reCAPTCHA on WordPress Login**.
4. Enter your Google reCAPTCHA v2 ID and Secret Key.
5. Save changes and open the login page to confirm the widget appears.

== Frequently Asked Questions ==

= Which reCAPTCHA version is supported? =

Google reCAPTCHA v2 ("I'm not a robot" Checkbox).

= What happens if I activate the plugin without values? =

Nothing changes on the login page until both the ID and Secret Key are saved.

= Where do I get my ID and Secret Key? =

From Google's reCAPTCHA admin console: https://www.google.com/recaptcha/admin

== Changelog ==

= 1.0.0 =
* Initial release.

== Upgrade Notice ==

= 1.0.0 =
Initial release.
