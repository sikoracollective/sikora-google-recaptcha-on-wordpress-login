<?php
/**
 * Plugin Name: Sikora Google reCAPTCHA on WordPress Login
 * Description: Adds Google reCAPTCHA v2 to the WordPress login page.
 * Version: 1.0.0
 * Author: Sikora Collective
 * License: GPL-2.0-or-later
 * Text Domain: sikora-google-recaptcha-on-wordpress-login
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // prevent direct access
}

define( 'SGRWL_VERSION', '1.0.0' );
define( 'SGRWL_PLUGIN_FILE', __FILE__ );
define( 'SGRWL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SGRWL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once SGRWL_PLUGIN_DIR . 'includes/class-sgrwl-settings.php';
require_once SGRWL_PLUGIN_DIR . 'includes/class-sgrwl-login.php';

/**
 * Bootstrap plugin hooks.
 */
function sgrwl_init() {
	$settings = new SGRWL_Settings();
	$settings->register();

	$login = new SGRWL_Login( $settings );
	$login->register();
}
add_action( 'plugins_loaded', 'sgrwl_init' );
