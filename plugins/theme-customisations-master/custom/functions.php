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

// getting rid of JS fixed position thingie that was breaking the page at times
add_action( 'wp_enqueue_scripts', 'jk_remove_sticky_checkout', 99 );

add_action( 'wp_enqueue_scripts', 'remove_product_zoom', 99 );

function remove_product_zoom () {
	remove_theme_support( 'wc-product-gallery-zoom' );
}

function jk_remove_sticky_checkout() {
	wp_dequeue_script( 'storefront-sticky-payment' );
}

// create new widget for the top of the homepage
// for a "more coming soon"-type of message
function homepageInfoBox( $content ) {
dynamic_sidebar('homepage-top-info-box');
	return $content;
}
add_filter( 'homepage', 'homepageInfoBox' );

register_sidebar( array(
	'id'          => 'homepage-top-info-box',
	'name'        => 'Homepage Top Info Box',
	'before_widget' => '<div class="info-box">',
	'after_widget'  => '</div>',
	'before_title'  => '<h3>',
	'after_title'   => '</h3>',
));

// get rid of the WooThemes credit in the footer
add_action( 'init', 'custom_remove_footer_credit', 10 );
function custom_remove_footer_credit () {
    remove_action( 'storefront_footer', 'storefront_credit', 20 );
    add_action( 'storefront_footer', 'custom_storefront_credit', 20 );
}
function custom_storefront_credit() {
	?>
	<div class="site-info">
		&copy; <?php echo get_bloginfo( 'name' ) . ' ' . date( 'Y' ); ?>
	</div><!-- .site-info -->
	<?php
}

if (!class_exists('WooCommerce'))
{
    exit;
}// Exit if WooCommerce is not active

// use http://localhost/checkout/order-received/428/?key=wc_order_58c6858771308 for testing JLWG on /order-received page
function wh_CustomReadOrder($order_id)
{
  //getting order object
  $order = wc_get_order($order_id);
  $items = $order->get_items();

  foreach ($items as $item_id => $item_data) {
		if ($item_data['product_id'] == 11) {
			include 'inc/recommendation-jlwg.php';
		}

		if ($item_data['item_meta']['_product_id'][0] == 416) {
			include 'inc/recommendation-bsp.php';
		}

  }
}

add_action('woocommerce_thankyou', 'wh_CustomReadOrder');
