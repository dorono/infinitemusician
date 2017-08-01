<?php
/*
	Plugin Name: PixelYourSite
	Description: Add the Facebook Pixel code into your Wordpress site and set up standard events with just a few clicks. Fully compatible with Woocommerce, purchase event included.
	Plugin URI: http://www.pixelyoursite.com/facebook-pixel-plugin-help
	Author: PixelYourSite
	Author URI: http://www.pixelyoursite.com
	Version: 5.0.5.2
	License: GPLv3
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'PYS_FREE_VERSION', '5.0.5.2' );

if ( ! function_exists( 'pys_is_pixelyoursite_pro_active' ) ) {

    /**
     * Check whatever PixelYourSite PRO version activated.
     *
     * @return bool
     */
    function pys_is_pixelyoursite_pro_active() {

        if ( ! function_exists( 'is_plugin_active' ) ) {
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        return is_plugin_active( 'pixelyoursite-pro/pixelyoursite-pro.php' );

    }

}

if ( ! function_exists( 'pys_free_plugin_activated' ) ) {

    register_activation_hook( __FILE__, 'pys_free_plugin_activated' );
    function pys_free_plugin_activated() {

        if ( pys_is_pixelyoursite_pro_active() ) {
            wp_die( 'Please deactivate PixelYourSite PRO version first.', 'Plugin Activation', array(
                'back_link' => true,
            ) );
        }

        $options = get_option( 'pixel_your_site' );

        if ( ! $options || ! isset( $options['general']['pixel_id'] ) || empty( $options['general']['pixel_id'] ) ) {

            require_once 'inc/general.php';
            pys_initialize_settings();

        }

    }

}

if ( ! pys_is_pixelyoursite_pro_active() ) {
    require_once 'inc/general.php';
}