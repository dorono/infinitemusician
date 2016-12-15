<?php
/**
 * Plugin Name: IgniteWoo Updater
 * Plugin URI: http://ignitewoo.com/products/
 * Description: Helps you manage licenses and receive updates for your IgniteWoo products.
 * Version: 2.1.9
 * Author: IgniteWoo.com
 * Author URI: http://ignitewoo.com/
 * Network: true
 * Requires at least: 3.8.1
 * Tested up to: 4.5.1
 *
 * Text Domain: ignition-updater
 */
/*
    Copyright 2012 - Ignition 
    Copyright 2012 - WooThemes

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( is_admin() && ( isset( $_POST['action'] ) && 'ignition_activate_license_keys' == $_POST['action'] ) ) {
	add_action( 'plugins_loaded', '__ignition_updater' );
} else if ( is_admin() && !defined( 'DOING_AJAX' ) ) {
	add_action( 'plugins_loaded', '__ignition_updater' );
}


function __ignition_updater () {
	global $ignition_updater_token; 
	
	$ignition_updater_token = 'ignitewoo-updater'; 
	
	require_once( 'classes/class-ignition-updater.php' );

	global $ignition_updater;
	$ignition_updater = new IgniteWoo_Updater( __FILE__, '2.0' );

	// Load the version from the plugin header in this file. This way we 
	// don't need to remember to change it anywhere else.
	$version = get_file_data( __FILE__, array( 'Version' ), '' );

	$ignition_updater->version = $version[0];
	$ignition_updater->admin->product_id = 'Updater';
	$ignition_updater->admin->licence_hash = '6471fb9bec3ef8e9dcafe3ba5bd994c8';
	$ignition_updater->admin->slug = plugin_basename( __FILE__ );
	$ignition_updater->admin->dir = dirname( __FILE__ );
}
