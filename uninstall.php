<?php
/**
 * Remove plugin data on uninstall.
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; // prevent direct access
}

/**
 * Delete plugin options for the current site.
 */
function sgrwl_delete_plugin_data() {
	delete_option( 'sgrwl_settings' );
}

if ( is_multisite() ) {
	$site_ids = get_sites(
		array(
			'fields' => 'ids',
			'number' => 0,
		)
	);

	foreach ( $site_ids as $site_id ) {
		switch_to_blog( (int) $site_id );
		sgrwl_delete_plugin_data();
		restore_current_blog();
	}
} else {
	sgrwl_delete_plugin_data();
}
