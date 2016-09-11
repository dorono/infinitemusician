<?php
/**
 * Functions.php
 *
 * @package  Theme_Customisations
 * @author   WooThemes
 * @since    1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * functions.php
 * Add PHP snippets here
 */
add_action( 'wp_enqueue_scripts', 'jk_remove_sticky_checkout', 99 );

function jk_remove_sticky_checkout() {
 wp_dequeue_script( 'storefront-sticky-payment' );
}

//Possible future use for overriding jump to top of page
// add_action( 'wp_enqueue_scripts', 'swap_onepage_checkout_js', 100 );
//
// function swap_onepage_checkout_js() {
// 	wp_dequeue_script('woocommerce-one-page-checkout');
// 	wp_enqueue_script('woocommerce-one-page-checkout-custom', '/wp-content/plugins/woocommerce-one-page-checkout/js/one-page-checkout-custom.js', array( 'jquery', 'wc-add-to-cart-variation' ), '1.0', true );
//
//
// }
