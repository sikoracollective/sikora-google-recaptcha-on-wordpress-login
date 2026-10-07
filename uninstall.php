<?php
/**
 * Remove all plugin-stored data when the plugin is deleted.
 *
 * Deletes the `slr_settings` option (and the legacy `sgrwl_settings` option)
 * on the current site, or on every site in a multisite network, so nothing
 * from the plugin remains.
 *
 * @package SLR
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; // prevent direct access
}

/**
 * Delete plugin options for the current site.
 *
 * @return void
 */
function slr_delete_plugin_data() {
	delete_option( 'slr_settings' );
	delete_option( 'sgrwl_settings' ); // legacy option name
}

if ( is_multisite() ) {
	$site_ids = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0, // all sites
		)
	);

	foreach ( $site_ids as $site_id ) {
		switch_to_blog( (int) $site_id );
		slr_delete_plugin_data();
		restore_current_blog();
	}
} else {
	slr_delete_plugin_data();
}
