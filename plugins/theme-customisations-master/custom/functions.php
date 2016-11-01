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
		&copy; <?php echo get_bloginfo( 'name' ) . ' ' . get_the_date( 'Y' ); ?>
	</div><!-- .site-info -->
	<?php
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

class Homestore_Template {
	public function single_product_title() { ?>
		<div id="hero">
			 <img src="<?php echo get_post_meta( get_the_ID(), 'header_img_url', true ); ?>" class="product-page-hero">
		 </div>
		<?php
	}
}

add_action( 'woocommerce_before_single_product_summary',  array( Homestore_Template, 'single_product_title' ), 10 );
