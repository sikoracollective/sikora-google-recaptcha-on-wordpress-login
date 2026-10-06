<?php
/**
 * Plugin Name:       Sikora Google reCAPTCHA on WordPress Login
 * Description:       Adds Google reCAPTCHA v2 to the WordPress login page.
 * Version:           1.1.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Sikora Collective
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       sikora-google-recaptcha-on-wordpress-login
 *
 * @package SGRWL
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // prevent direct access
}

define( 'SGRWL_VERSION', '1.1.0' );                          // plugin version
define( 'SGRWL_PLUGIN_FILE', __FILE__ );                     // main plugin file path
define( 'SGRWL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );   // plugin directory path
define( 'SGRWL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );    // plugin directory url

require_once SGRWL_PLUGIN_DIR . 'includes/class-sgrwl-settings.php';
require_once SGRWL_PLUGIN_DIR . 'includes/class-sgrwl-login.php';

/**
 * Bootstrap settings and login integrations after plugins load.
 *
 * @return void
 */
function sgrwl_init() {
	$settings = new SGRWL_Settings();
	$settings->register();

	$login = new SGRWL_Login( $settings );
	$login->register();
}
add_action( 'plugins_loaded', 'sgrwl_init' );
