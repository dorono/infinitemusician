<?php
/*
Version 1.0.1 - January 5, 2013
 	Changed priority of plugins_api hook

Version 1.0.0 - December, 2013
	Minor reworking of variable values
	Added custom function for plugin row notices
*/
/**
 * Queue updates for the Updater
 */
if ( ! function_exists( 'ignitewoo_queue_update' ) ) {
	function ignitewoo_queue_update( $file, $file_id, $product_id ) {
		global $ignitewoo_queued_updates;

		if ( ! isset( $ignitewoo_queued_updates ) )
			$ignitewoo_queued_updates = array();

		$plugin             = new stdClass();
		$plugin->file       = $file;
		$plugin->file_id    = $file_id;
		$plugin->product_id = $product_id;

		
		$ignitewoo_queued_updates[] = $plugin;
	}
}

if (!function_exists( 'ignite_plugin_update_row')) {
function ignite_plugin_update_row( $file, $plugin_data ) {

	$this_plugin_base = plugin_basename( $file );

	$msg = get_option( 'plugin_err_' . $this_plugin_base, false );
	
	$wp_list_table = _get_list_table('WP_Plugins_List_Table');
		
	if ( !empty( $msg ) ) { 

		echo '<tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange"><div class="update-message" style="border-left:4px solid #D54E21; margin-left:0">';

		echo $msg;

		echo '</div></td></tr>';
	}
	
	global $ignitewoo_updater_activated;
	
	if ( empty( $ignitewoo_updater_activated ) || !$ignitewoo_updater_activated )
		$ignitewoo_updater_activated = get_option( 'ignitewoo-updater-activated', false );

	$got_it = false;
		
	if ( isset( $ignitewoo_updater_activated ) && !empty( $ignitewoo_updater_activated ) && is_array( $ignitewoo_updater_activated ) && count( $ignitewoo_updater_activated ) > 0 )
	foreach( $ignitewoo_updater_activated as $k => $v ) {

		if ( $k == $this_plugin_base ) {
			$got_it = true; 
			break;
		}
		
	}

	// Updater not installed yet? Show that message
	if ( ! class_exists( 'IgniteWoo_Updater' ) ) {

		$installed = false;
		
		$plugins = array_keys( get_plugins() );

		if ( count( $plugins ) )
		foreach ( $plugins as $plugin ) {

			if ( strpos( $plugin, 'ignitewoo-updater.php' ) !== false ) {

				$url = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=ignitewoo-updater%2Fignitewoo-updater.php' . '&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_ignitewoo-updater/ignitewoo-updater.php' );

				echo '<tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange"><div class="update-message" style="border-left:4px solid #D54E21; margin-left:0">';
				
				echo '<span>' . __( 'Automatic update is not available for this plugin.', 'ignitewoo' ) . '</span> ';
				
				echo '<span style="color:#cf0000">';

				_e( sprintf( '<a href="%s">Active the IgniteWoo Updater</a> to receive updates and support', $url ), 'ignitewoo' );

				echo '</span></div></td></tr>';

				$installed = true;
				
				break; 
			}

		}

		if ( $installed )
			return;
			
		$slug = 'ignitewoo-updater';

		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $slug ), 'install-plugin_' . $slug );
		
		echo '<tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange"><div class="update-message" style="border-left:4px solid #D54E21; margin-left:0"><span style="color:#cf0000">';

		_e( sprintf( '<a href="%s">Install the IgniteWoo Updater</a> so that you can activate your license key to receive updates and support', $install_url ), 'ignitewoo' );

		echo '</span></div></td></tr>';
		
	} else if ( empty( $ignitewoo_updater_activated ) || !$ignitewoo_updater_activated  ) {
	
		$activate_url = 'plugins.php?action=activate&plugin=' . urlencode( 'ignitewoo-updater/ignitewoo-updater.php' ) . '&plugin_status=all&paged=1&s&_wpnonce=' . urlencode( wp_create_nonce( 'activate-plugin_ignitewoo-updater/ignitewoo-updater.php' ) );
		
		echo '<tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange"><div class="update-message" style="border-left:4px solid #D54E21; margin-left:0"><span style="color:#cf0000">';

		_e( sprintf( '<a href="%s">Automatic update is not available for this plugin. Install and activate the IgniteWoo Updater plugin.', esc_url( admin_url( $activate_url ) ), 'ignitewoo' ) );


		echo '</span></div></td></tr>';
	
	} else if ( !$got_it ) {
	
		echo '<tr class="plugin-update-tr"><td colspan="' . $wp_list_table->get_column_count() . '" class="plugin-update colspanchange"  ><div class="update-message" style="border-left:4px solid #D54E21; margin-left:0">';
		
		echo '<span>' . __( 'There may be a new version of this plugin, but automatic update is unavailable.', 'ignitewoo' ) . '</span>';
		
		echo ' <span style="color:#cf0000">';

		_e( sprintf( '<a href="%s">Activate or renew your license key</a> to receive updates and support', admin_url('index.php?page=ignitewoo-licenses') ), 'ignitewoo' );

		echo '</span></div></td></tr>';
	}

}
}
/**
 * Load installer for the IgniteWoo Updater.
 * @return $api Object
 */
if ( ! class_exists( 'IgniteWoo_Updater' ) && ! function_exists( 'ignitewoo_updater_install' ) ) {
	function ignitewoo_updater_install( $api, $action, $args ) {
	
		$download_url = 'http://ignitewoo.com/api/ignitewoo-updater.zip';

		if ( 'plugin_information' != $action ||
			false !== $api ||
			! isset( $args->slug ) ||
			'ignitewoo-updater' != $args->slug
		) return $api;

		$api = new stdClass();
		$api->name = 'IgniteWoo Updater';
		$api->version = '1.0.0';
		$api->download_link = esc_url( $download_url );
		return $api;
	}

	add_filter( 'plugins_api', 'ignitewoo_updater_install', 9999, 3 );
}

/**
 * Updater Installation Prompts
 */
if ( ! class_exists( 'IgniteWoo_Updater' ) && ! function_exists( 'ignitewoo_updater_notice' ) ) {

	/**
	 * Display a notice if the "IgniteWoo Updater" plugin hasn't been installed.
	 * @return void
	 */
	function ignitewoo_updater_notice() {
	
		$active_plugins = apply_filters( 'active_plugins', get_option('active_plugins' ) );
		
		if ( in_array( 'ignitewoo-updater/ignitewoo-updater.php', $active_plugins ) )
			return;

		$slug = 'ignitewoo-updater';
		
		$install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $slug ), 'install-plugin_' . $slug );
		
		$activate_url = 'plugins.php?action=activate&plugin=' . urlencode( 'ignitewoo-updater/ignitewoo-updater.php' ) . '&plugin_status=all&paged=1&s&_wpnonce=' . urlencode( wp_create_nonce( 'activate-plugin_ignitewoo-updater/ignitewoo-updater.php' ) );

		$message = '<a href="' . esc_url( $install_url ) . '">Install the IgniteWoo Updater plugin</a> to get updates and support for your IgniteWoo plugins.';
		
		$is_downloaded = false;
		
		$plugins = array_keys( get_plugins() );

		foreach ( $plugins as $plugin ) {
			if ( strpos( $plugin, 'ignitewoo-updater.php' ) !== false ) {
				$is_downloaded = true;
				$message = '<a href="' . esc_url( admin_url( $activate_url ) ) . '">Activate the IgniteWoo Updater plugin</a> to get updates and support for your IgniteWoo plugins.';
			}
		}

		echo '<div class="updated fade"><p>' . $message . '</p></div>' . "\n";
	}

	add_action( 'admin_notices', 'ignitewoo_updater_notice' );
}

/**
 * Prevent conflicts with older versions
 */
if ( ! class_exists( 'IgniteWoo_Plugin_Updater' ) ) {
	class IgniteWoo_Plugin_Updater { function init() {} }
}
